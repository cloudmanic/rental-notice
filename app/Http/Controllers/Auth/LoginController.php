<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log the login activity
            $user = Auth::user();
            ActivityService::log(
                "{$user->first_name} {$user->last_name} logged in.",
                null,
                null,
                null,
                'User'
            );

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Get user info before logging out
        $user = Auth::user();
        $userName = $user ? "{$user->first_name} {$user->last_name}" : 'User';

        // Log the logout activity before logging out
        if ($user) {
            ActivityService::log(
                "{$userName} logged out.",
                null,
                null,
                null,
                'User'
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
