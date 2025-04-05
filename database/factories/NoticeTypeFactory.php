<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NoticeType>
 */
class NoticeTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'price' => fake()->randomFloat(2, 10, 100),
            'plan_date' => Carbon::now(),
        ];
    }
}
