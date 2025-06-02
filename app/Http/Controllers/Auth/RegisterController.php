<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Notifications\UserRegistered;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Start database transaction
        try {
            return \DB::transaction(function () use ($validated) {
                // Create account
                $account = Account::create([
                    'name' => $validated['company_name'] ?? "{$validated['first_name']} {$validated['last_name']}'s Company",
                ]);

                // Create user
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'type' => User::TYPE_ADMIN, // Set default type to Admin
                ]);

                // Attach user to account as owner
                $account->users()->attach($user, ['is_owner' => true]);

                // Log the user in
                Auth::login($user);

                // Log the registration activity
                ActivityService::log(
                    "{$user->first_name} {$user->last_name} created a new account.",
                    null,
                    null,
                    null,
                    'User'
                );

                // Send Slack notification
                $user->notify(new UserRegistered($user, $account->name));

                return redirect()->route('dashboard')->with('success', 'Account created successfully!');
            });
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'There was a problem creating your account. Please try again.']);
        }
    }
}
