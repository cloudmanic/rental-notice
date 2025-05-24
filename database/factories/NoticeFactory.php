<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notice>
 */
class NoticeFactory extends Factory
{
    protected $model = Notice::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
            'agent_id' => Agent::factory(),
            'notice_type_id' => fake()->randomElement([1, 2]), // Use existing notice types
            'price' => 50.00, // Fixed price from notice types
            'past_due_rent' => fake()->randomFloat(2, 500, 5000),
            'late_charges' => fake()->randomFloat(2, 0, 500),

            // Other charges
            'other_1_title' => fake()->optional()->words(3, true),
            'other_1_price' => fake()->optional()->randomFloat(2, 10, 500),
            'other_2_title' => fake()->optional()->words(3, true),
            'other_2_price' => fake()->optional()->randomFloat(2, 10, 500),
            'other_3_title' => fake()->optional()->words(3, true),
            'other_3_price' => fake()->optional()->randomFloat(2, 10, 500),
            'other_4_title' => fake()->optional()->words(3, true),
            'other_4_price' => fake()->optional()->randomFloat(2, 10, 500),
            'other_5_title' => fake()->optional()->words(3, true),
            'other_5_price' => fake()->optional()->randomFloat(2, 10, 500),

            // Flags
            'payment_other_means' => fake()->boolean(),

            // Status and error fields
            'status' => fake()->randomElement([
                Notice::STATUS_PENDING_PAYMENT,
                Notice::STATUS_SERVICE_PENDING,
                Notice::STATUS_SERVED,
                Notice::STATUS_ERROR,
            ]),
            'error_message' => fn (array $attributes) => $attributes['status'] === Notice::STATUS_ERROR
                ? fake()->sentence()
                : null,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Notice $notice) {
            // Create and attach a tenant
            $tenant = Tenant::factory()->create(['account_id' => $notice->account_id]);
            $notice->tenants()->attach($tenant->id);

            // Randomly add 0-2 additional tenants
            $additionalTenantsCount = fake()->numberBetween(0, 2);
            if ($additionalTenantsCount > 0) {
                $additionalTenants = Tenant::factory()
                    ->count($additionalTenantsCount)
                    ->create(['account_id' => $notice->account_id]);

                foreach ($additionalTenants as $additionalTenant) {
                    $notice->tenants()->attach($additionalTenant->id);
                }
            }
        });
    }

    // Add specific state methods if needed
    public function pendingPayment()
    {
        return $this->state(function (array $attributes) {
            return ['status' => Notice::STATUS_PENDING_PAYMENT];
        });
    }

    public function servicePending()
    {
        return $this->state(function (array $attributes) {
            return ['status' => Notice::STATUS_SERVICE_PENDING];
        });
    }

    public function served()
    {
        return $this->state(function (array $attributes) {
            return ['status' => Notice::STATUS_SERVED];
        });
    }

    public function error()
    {
        return $this->state(function (array $attributes) {
            return ['status' => Notice::STATUS_ERROR];
        });
    }
}
