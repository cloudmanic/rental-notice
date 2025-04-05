<?php

namespace Database\Factories;

use App\Models\NoticeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Property Management',
            'notice_type_plan_date' => NoticeType::getMostRecentPlanDate(),
        ];
    }
}
