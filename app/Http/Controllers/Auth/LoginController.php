<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Regenerate session token to prevent expiration issues
        request()->session()->regenerateToken();
        return view('auth.login');
    }

    /**
     * Handle login request. Accepts either username (name) or email address.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $login)->orWhere('name', $login)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'model_type' => 'Auth',
                'model_id' => null,
                'description' => 'Logged in',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended('/home');
        }

        throw ValidationException::withMessages([
            'login' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'model_type' => 'Auth',
                'model_id' => null,
                'description' => 'Logged out',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

