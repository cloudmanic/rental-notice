<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static float getStandardPrice()
 * @method static float|null getBulkPrice(int $quantity)
 * @method static array getBulkPrices()
 * @method static bool isEligibleForBulkPricing(int $quantity)
 * @see \App\Services\PricingService
 */
class Pricing extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pricing';
    }
}
