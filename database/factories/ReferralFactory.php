<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Referral;
use App\Models\Referrer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referrer_id' => Referrer::factory(),
            'account_id' => Account::factory(),
            'discount_amount' => 3.00,
            'discount_percentage' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Referral $referral) {
            // Update the account's plan_date to match the referrer's
            $referral->account->update([
                'notice_type_plan_date' => $referral->referrer->plan_date,
            ]);
        });
    }
}
