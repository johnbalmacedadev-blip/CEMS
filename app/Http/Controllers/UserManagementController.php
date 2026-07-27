<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPagePermission;
use App\Support\PermissionGroups;
use App\Support\RoleTemplates;
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
        $templates = RoleTemplates::all();

        return view('settings.users.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $templateKeys = implode(',', array_keys(RoleTemplates::all()));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:admin,user'],
            'permission_template' => ['required', 'in:'.$templateKeys],
        ]);

        $templateKey = $data['permission_template'] ?? null;
        unset($data['permission_template']);

        // Selecting Super Admin template forces admin role
        if ($templateKey === 'super_admin') {
            $data['role'] = 'admin';
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if ($templateKey === 'super_admin' || $user->isAdmin()) {
            $user->pagePermissions()->delete();
        } else {
            RoleTemplates::applyToUser($user, $templateKey ?: 'spectator');
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User created successfully.')
            ->with('swal_title', 'Saved');
    }

    public function edit(User $user)
    {
        $templates = RoleTemplates::all();

        return view('settings.users.edit', compact('user', 'templates'));
    }

    public function update(Request $request, User $user)
    {
        $templateKeys = implode(',', array_keys(RoleTemplates::all()));

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,user'],
            'permission_template' => ['nullable', 'in:'.$templateKeys],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }
        $data = $request->validate($rules);

        $templateKey = $data['permission_template'] ?? null;
        unset($data['permission_template']);

        if ($templateKey === 'super_admin') {
            $data['role'] = 'admin';
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        if ($templateKey) {
            RoleTemplates::applyToUser($user->fresh(), $templateKey);
        } elseif ($user->fresh()->isAdmin()) {
            $user->pagePermissions()->delete();
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User updated successfully.')
            ->with('swal_title', 'Saved');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('settings.users.index')
                ->with('error', 'You cannot delete your own account.')
                ->with('swal_title', 'Error');
        }
        $user->delete();

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User deleted successfully.')
            ->with('swal_title', 'Deleted');
    }

    public function permissions(User $user)
    {
        $pages = config('pages.list', []);
        $permissionGroups = PermissionGroups::forPermissionsMatrix();
        $permissions = [];
        foreach (array_keys($pages) as $slug) {
            $permissions[$slug] = $user->getPagePermission($slug);
        }
        $templates = RoleTemplates::all();
        $templatePermissions = [];
        foreach (array_keys($templates) as $key) {
            $templatePermissions[$key] = RoleTemplates::buildPermissions($key);
        }

        return view('settings.users.permissions', compact(
            'user',
            'pages',
            'permissions',
            'permissionGroups',
            'templates',
            'templatePermissions'
        ));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $templateKeys = implode(',', array_keys(RoleTemplates::all()));

        $request->validate([
            'apply_template' => ['nullable', 'in:'.$templateKeys],
        ]);

        if ($request->filled('apply_template')) {
            $key = $request->input('apply_template');
            $label = RoleTemplates::get($key)['label'] ?? 'template';
            RoleTemplates::applyToUser($user, $key);

            if ($key === 'super_admin') {
                return redirect()
                    ->route('settings.users.index')
                    ->with('success', $user->name.' is now a Super Admin with full access.')
                    ->with('swal_title', 'Template applied');
            }

            return redirect()
                ->route('settings.users.permissions', $user)
                ->with('success', 'Applied "'.$label.'" template. Review the checkboxes and click Save Permissions if you need further tweaks.')
                ->with('swal_title', 'Template applied');
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('settings.users.index')
                ->with('info', 'Super Admins always have full access.');
        }

        $pages = config('pages.list', []);
        $input = $request->input('permissions', []);

        foreach (array_keys($pages) as $slug) {
            $p = $input[$slug] ?? [];
            UserPagePermission::updateOrCreate(
                ['user_id' => $user->id, 'page_slug' => $slug],
                [
                    'can_view' => ! empty($p['can_view']),
                    'can_create' => ! empty($p['can_create']),
                    'can_update' => ! empty($p['can_update']),
                    'can_delete' => ! empty($p['can_delete']),
                ]
            );
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Permissions updated for '.$user->name.'.')
            ->with('swal_title', 'Saved');
    }
}
