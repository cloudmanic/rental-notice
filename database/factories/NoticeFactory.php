<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Models\Notice;
use App\Models\NoticeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notice>
 */
class NoticeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'user_id' => User::factory(),
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

            // Agent information
            'agent_name' => fake()->name(),
            'agent_address_1' => fake()->streetAddress(),
            'agent_address_2' => fake()->optional()->secondaryAddress(),
            'agent_city' => fake()->city(),
            'agent_state' => fake()->stateAbbr(),
            'agent_zip' => fake()->postcode(),
            'agent_phone' => fake()->phoneNumber(),
            'agent_email' => fake()->email(),

            // Flags
            'payment_other_means' => fake()->boolean(),
            'include_all_other_occupents' => fake()->boolean(),

            // Status and error fields
            'status' => fake()->randomElement(Notice::statuses()),
            'error_message' => fn(array $attributes) =>
            $attributes['status'] === Notice::STATUS_ERROR
                ? fake()->sentence()
                : null,
        ];
    }
}
