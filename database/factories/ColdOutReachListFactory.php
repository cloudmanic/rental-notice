<?php

namespace Database\Factories;

use App\Models\ColdOutReachList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ColdOutReachList>
 */
class ColdOutReachListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a company domain
        $companyName = $this->faker->company();
        $domain = strtolower(
            preg_replace('/[^a-zA-Z0-9]/', '', $companyName).
                '.'.
                $this->faker->randomElement(['com', 'net', 'org', 'io'])
        );

        return [
            'type' => $this->faker->randomElement(['Property Manager', 'Real Estate Agent']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'street_1' => $this->faker->streetAddress(),
            'street_2' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'zip' => $this->faker->postcode(),
            'expiration' => $this->faker->dateTimeBetween('+1 month', '+2 years'),
            'license_number' => $this->faker->numerify('LIC-#####'),
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Pending', 'Expired']),
            'company_name' => $companyName,
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'domain' => $domain,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (ColdOutReachList $coldOutReachList) {
            // Any adjustments after making the model
        })->afterCreating(function (ColdOutReachList $coldOutReachList) {
            // Any adjustments after creating the model
        });
    }

    /**
     * Indicate that the contact is a property manager.
     */
    public function propertyManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Property Manager',
        ]);
    }

    /**
     * Indicate that the contact is a real estate agent.
     */
    public function realEstateAgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Real Estate Agent',
        ]);
    }
}
