<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class StripeCheckoutController extends Controller
{
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

        // Create a session link to redirect the user to for payment. 
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
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
            'success_url' => url('/dashboard?notices=success'),
            'cancel_url' => url('/dashboard?notices=cancel'),
        ]);

        return Redirect::away($session->url);
    }
}
