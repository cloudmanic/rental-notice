<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\NoticePaid;

class StripeCheckoutController extends Controller
{
    /**
     * Create a Stripe Checkout session for the given notice
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Notice $notice)
    {
        // Require Stripe PHP SDK
        if (!class_exists('Stripe\\Stripe')) {
            abort(500, 'Stripe PHP SDK is not installed. Run composer require stripe/stripe-php');
        }

        // Check if the notice belongs to the authenticated user's account
        $account = Auth::user()->account;
        if ($notice->account_id !== $account->id) {
            abort(403, 'You do not have permission to access this notice');
        }

        if (! $account->owners->first()) {
            abort(404, 'No owners found for this account');
        }

        $owner = $account->owners->first();

        // Get the notice type and stripe price ID
        $noticeType = $notice->noticeType;
        if (!$noticeType || !$noticeType->stripe_price_id) {
            abort(404, 'No valid stripe price ID found for this notice type');
        }

        // Save the notice we are checking out in the session
        session(['checkout_notice_id' => $notice->id]);

        // Log that we are starting the checkout process
        Log::info('Stripe Checkout Initiated', [
            'notice_id' => $notice->id,
            'user_id' => Auth::id(),
            'stripe_price_id' => $noticeType->stripe_price_id,
        ]);

        // Create a session link to redirect the user to for payment. 
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'customer_email' => $owner->email,
            'line_items' => [[
                'price' => $noticeType->stripe_price_id,
                'quantity' => 1,
            ]],
            'allow_promotion_codes' => false,
            'payment_intent_data' => [
                'setup_future_usage' => 'off_session', // enables saving payment method
            ],
            'success_url' => route('stripe.checkout.success'),
            'cancel_url' => route('stripe.checkout.cancel'),
        ]);

        return Redirect::away($session->url);
    }

    /**
     * Handle the success redirect from Stripe checkout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        // Get the notice id from the session
        $noticeId = session('checkout_notice_id');

        if (!$noticeId) {
            abort(404, 'No notice found in session');
        }

        // unset the session variable
        session()->forget('checkout_notice_id');

        // Find the notice and update its status
        $notice = Notice::find($noticeId);
        if (!$notice) {
            abort(404, 'Notice not found');
        }
        $notice->status = Notice::STATUS_SERVICE_PENDING;
        $notice->save();

        // Send notices that payment was successful. Email to user, slack to us.
        $user = $request->user();
        $user->notify(new NoticePaid($notice));

        // Build the success message
        $tenants = $notice->tenants->map(function ($tenant) {
            return $tenant->first_name . ' ' . $tenant->last_name;
        })->implode(', ');

        $message = "Payment was successfully processed. You will receive an email when the service to $tenants is complete.";

        // Log for debugging purposes
        Log::info('Stripe Checkout Success', [
            'notice_id' => $notice->id,
            'user_id' => Auth::id(),
            'message' => $message,
        ]);

        session()->flash('success', $message);
        return to_route('notices.index');
    }

    /**
     * Handle the cancel redirect from Stripe checkout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel()
    {
        // Get the notice id from the session
        $noticeId = session('checkout_notice_id');

        if (!$noticeId) {
            abort(404, 'No notice found in session');
        }

        // unset the session variable
        session()->forget('checkout_notice_id');

        // Log for debugging purposes
        Log::info('Stripe Checkout Cancelled', [
            'notice_id' => $noticeId,
            'user_id' => Auth::id(),
        ]);

        session()->flash('error', 'Payment was cancelled or failed.');
        return to_route('notices.index');
    }
}
