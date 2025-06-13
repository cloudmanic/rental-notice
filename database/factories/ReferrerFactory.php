<?php

namespace Database\Factories;

use App\Models\NoticeType;
use App\Models\Referrer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referrer>
 */
class ReferrerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'slug' => Referrer::generateUniqueSlug($firstName, $lastName),
            'plan_date' => NoticeType::getMostRecentPlanDate() ?? now()->format('Y-m-d'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the referrer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
