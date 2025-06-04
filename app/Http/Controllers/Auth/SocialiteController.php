<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SubscribeUserToSendyJob;
use App\Models\Account;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the OAuth provider.
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the OAuth provider.
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception) {
            return redirect()->route('login')->withErrors(['social' => 'Unable to authenticate with '.ucfirst($provider).'. Please try again.']);
        }

        // Check if user already exists by email
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update social login fields if they don't exist
            if (! $user->{$provider.'_id'}) {
                $user->update([
                    $provider.'_id' => $socialUser->getId(),
                    $provider.'_avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($user);

            // Log activity
            ActivityService::log(
                description: 'User logged in via '.ucfirst($provider),
                event: 'User'
            );

            return redirect()->intended('/dashboard');
        }

        // Create new user
        $user = $this->createUser($socialUser, $provider);

        Auth::login($user);

        // Trigger the registered event
        event(new Registered($user));

        // Subscribe user to mailing list
        SubscribeUserToSendyJob::dispatch($user, 'social_login_'.strtolower($provider), request()->ip());

        // Log activity
        ActivityService::log(
            description: 'New user registered via '.ucfirst($provider),
            event: 'User'
        );

        return redirect()->intended('/dashboard');
    }

    /**
     * Create a new user from social login data.
     */
    protected function createUser($socialUser, string $provider): User
    {
        // Create account first
        $account = Account::create([
            'name' => $this->extractAccountName($socialUser->getName()),
        ]);

        // Extract first and last name from full name
        $nameParts = explode(' ', trim($socialUser->getName()));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';

        // Create user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(32)), // Random password since they'll use social login
            $provider.'_id' => $socialUser->getId(),
            $provider.'_avatar' => $socialUser->getAvatar(),
            'type' => 'Admin', // Default to admin for social logins
        ]);

        // Attach user to account with ownership
        $user->accounts()->attach($account->id, ['is_owner' => true]);

        return $user;
    }

    /**
     * Extract account name from user's full name.
     */
    protected function extractAccountName(string $name): string
    {
        $parts = explode(' ', trim($name));
        $firstName = $parts[0] ?? '';

        return ucfirst($firstName).'\'s Account';
    }

    /**
     * Validate the OAuth provider.
     */
    protected function validateProvider(string $provider): void
    {
        if (! in_array($provider, ['google', 'apple'])) {
            abort(404);
        }
    }
}
