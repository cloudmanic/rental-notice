<?php

namespace App\Http\Controllers;

use App\Models\Referrer;
use App\Services\PricingService;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /**
     * Display the referral landing page.
     */
    public function show(string $slug, PricingService $pricingService): View
    {
        $referrer = Referrer::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Set cookie to track the referral (expires in 30 days)
        cookie()->queue('referrer_id', $referrer->id, 60 * 24 * 30);

        $standardPrice = $pricingService->getStandardPrice();
        $discountedPrice = $referrer->discounted_price;
        $discountAmount = $referrer->discount_amount;

        return view('marketing.referral', compact(
            'referrer',
            'standardPrice',
            'discountedPrice',
            'discountAmount'
        ));
    }
}
