<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class AccountImpersonationController extends Controller
{
    /**
     * Impersonate the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate(User $user)
    {
        // Check if the current user has permission to impersonate
        if (!Auth::user() || Auth::user()->type !== User::TYPE_SUPER_ADMIN) {
            abort(403, 'Unauthorized action.');
        }

        // Store the original user ID in the session
        $originalUserId = Auth::id();
        Session::put('impersonating', true);
        Session::put('original_user_id', $originalUserId);

        // Log in as the target user
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Leave impersonation mode and return to original user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leave()
    {
        // Check if we're actually impersonating
        if (!Session::has('original_user_id')) {
            return redirect()->route('dashboard');
        }

        // Get the original user
        $originalUserId = Session::get('original_user_id');
        $originalUser = User::find($originalUserId);

        if (!$originalUser) {
            // If original user somehow doesn't exist anymore, just log out
            Auth::logout();
            Session::forget('impersonating');
            Session::forget('original_user_id');
            return redirect()->route('login');
        }

        // Log back in as the original user
        Auth::login($originalUser);

        // Clear impersonation session data
        Session::forget('impersonating');
        Session::forget('original_user_id');

        // Redirect back to the accounts page
        return redirect()->route('accounts.index')->with('message', 'Returned to your account.');
    }
}
