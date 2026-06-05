<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPagePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        return view('settings.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,user'],
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('settings.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('settings.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,user'],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }
        $data = $request->validate($rules);
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('settings.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users.index')->with('error', 'You cannot delete your own account.');
        }
        $user->delete();
        return redirect()->route('settings.users.index')->with('success', 'User deleted successfully.');
    }

    public function permissions(User $user)
    {
        $pages = config('pages.list', []);
        $permissions = [];
        foreach (array_keys($pages) as $slug) {
            $permissions[$slug] = $user->getPagePermission($slug);
        }
        return view('settings.users.permissions', compact('user', 'pages', 'permissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $pages = config('pages.list', []);
        $input = $request->input('permissions', []);

        foreach (array_keys($pages) as $slug) {
            $p = $input[$slug] ?? [];
            $canView = ! empty($p['can_view']);
            $canCreate = ! empty($p['can_create']);
            $canUpdate = ! empty($p['can_update']);
            $canDelete = ! empty($p['can_delete']);

            UserPagePermission::updateOrCreate(
                ['user_id' => $user->id, 'page_slug' => $slug],
                [
                    'can_view' => $canView,
                    'can_create' => $canCreate,
                    'can_update' => $canUpdate,
                    'can_delete' => $canDelete,
                ]
            );
        }

        return redirect()->route('settings.users.index')->with('success', 'Permissions updated for ' . $user->name . '.');
    }
}
