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
}
