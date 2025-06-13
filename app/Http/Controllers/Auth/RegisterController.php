<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SubscribeUserToSendyJob;
use App\Models\Account;
use App\Models\Referral;
use App\Models\Referrer;
use App\Models\User;
use App\Notifications\UserRegistered;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $user = null;
            $account = null;
            $referrer = null;

            \DB::transaction(function () use ($validated, $request, &$user, &$account, &$referrer) {
                // Check for referral cookie
                $referrerId = $request->cookie('referrer_id');

                if ($referrerId) {
                    $referrer = Referrer::where('id', $referrerId)
                        ->where('is_active', true)
                        ->first();
                }

                // Create account with referrer's plan_date if applicable
                $accountData = [
                    'name' => $validated['company_name'] ?? "{$validated['first_name']} {$validated['last_name']}'s Company",
                ];

                if ($referrer) {
                    $accountData['notice_type_plan_date'] = $referrer->plan_date;
                }

                $account = Account::create($accountData);

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

                // Create referral record if applicable
                if ($referrer) {
                    Referral::createFromReferrer($referrer, $account);
                }

                // Log the user in
                Auth::login($user);

                // Log the registration activity
                $activityMessage = "{$user->first_name} {$user->last_name} created a new account.";
                if ($referrer) {
                    $activityMessage .= " (Referred by {$referrer->full_name})";
                }

                ActivityService::log(
                    $activityMessage,
                    null,
                    null,
                    null,
                    'User'
                );
            });

            // Send notifications and dispatch jobs after transaction commits
            if ($user && $account) {
                // Send welcome email notification with referrer info if applicable
                $user->notify(new UserRegistered($user, $account->name, $referrer));

                // Subscribe user to email list
                SubscribeUserToSendyJob::dispatch($user, 'registration', $request->ip());
            }

            // Clear the referral cookie after successful registration
            return redirect()->route('dashboard')
                ->with('success', 'Account created successfully!')
                ->withoutCookie('referrer_id');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'There was a problem creating your account. Please try again.']);
        }
    }
}
