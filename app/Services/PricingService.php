<?php

namespace App\Services;

use App\Models\Account;
use App\Models\NoticeType;
use Illuminate\Support\Collection;

class PricingService
{
    /**
     * Get notice types available for an account based on their plan date
     *
     * @param Account $account The account to get notice types for
     * @return Collection Collection of available notice types
     */
    public function getNoticeTypesForAccount(Account $account): Collection
    {
        // If no plan date is set for the account, use the most recent plan date
        $planDate = $account->notice_type_plan_date ?? NoticeType::getMostRecentPlanDate();

        // If still no plan date (no notice types exist yet), return empty collection
        if (!$planDate) {
            return collect();
        }

        // Get all notice types with a plan date less than or equal to the account's plan date
        return NoticeType::where('plan_date', '<=', $planDate)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the most recent notice type plan date
     *
     * @return string|null The most recent plan date
     */
    public function getMostRecentPlanDate()
    {
        return NoticeType::getMostRecentPlanDate();
    }

    /**
     * Set the most recent plan date for an account
     *
     * @param Account $account The account to update
     * @return void
     */
    public function setAccountToMostRecentPlan(Account $account): void
    {
        $account->notice_type_plan_date = $this->getMostRecentPlanDate();
        $account->save();
    }

    /**
     * Get the standard price for a notice.
     *
     * @return float
     */
    public function getStandardPrice(): float
    {
        return 15.00;
    }

    /**
     * Get the bulk pricing for notices based on quantity.
     *
     * @param int $quantity
     * @return float|null
     */
    public function getBulkPrice(int $quantity): ?float
    {
        // Implement bulk pricing logic
        if ($quantity >= 50) {
            return 12.00;
        } elseif ($quantity >= 20) {
            return 13.00;
        } elseif ($quantity >= 10) {
            return 14.00;
        }

        return null; // Not eligible for bulk pricing
    }

    /**
     * Get all bulk price tiers.
     *
     * @return array
     */
    public function getBulkPrices(): array
    {
        return [
            [
                'quantity' => 10,
                'price' => 14.00,
                'savings' => '6.7%'
            ],
            [
                'quantity' => 20,
                'price' => 13.00,
                'savings' => '13.3%'
            ],
            [
                'quantity' => 50,
                'price' => 12.00,
                'savings' => '20%'
            ]
        ];
    }

    /**
     * Check if the given quantity is eligible for bulk pricing.
     *
     * @param int $quantity
     * @return bool
     */
    public function isEligibleForBulkPricing(int $quantity): bool
    {
        return $quantity >= 10;
    }
}
