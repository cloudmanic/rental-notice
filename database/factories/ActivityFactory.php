<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default values - can be overridden by specific states
        return [
            'account_id' => Account::inRandomOrder()->first()->id ?? Account::factory()->create()->id,
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'tenant_id' => null,
            'notice_id' => null,
            'agent_id' => null,
            'description' => $this->faker->sentence(),
            'event' => 'Account', // Default event type
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the activity is for a tenant.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function tenant()
    {
        $tenant = Tenant::inRandomOrder()->first() ?? Tenant::factory()->create();
        $action = $this->faker->randomElement(['created', 'updated', 'deleted', 'viewed']);

        return $this->state(function (array $attributes) use ($tenant, $action) {
            return [
                'tenant_id' => $tenant->id,
                'description' => ucfirst($action).' tenant: '.$tenant->name,
                'event' => 'Tenant',
            ];
        });
    }

    /**
     * Indicate that the activity is for a notice.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function notice()
    {
        // Create dependencies if needed
        $account = Account::inRandomOrder()->first() ?? Account::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $noticeType = NoticeType::inRandomOrder()->first() ?? NoticeType::factory()->create();

        // Create notice with explicit required fields
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
        ]);

        $action = $this->faker->randomElement(['created', 'updated', 'sent', 'deleted', 'viewed']);

        return $this->state(function (array $attributes) use ($notice, $action, $account) {
            return [
                'account_id' => $account->id,
                'notice_id' => $notice->id,
                'description' => ucfirst($action).' notice #'.$notice->id,
                'event' => 'Notice',
            ];
        });
    }

    /**
     * Indicate that the activity is for an agent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function agent()
    {
        $agent = Agent::inRandomOrder()->first() ?? Agent::factory()->create();
        $action = $this->faker->randomElement(['created', 'updated', 'deleted', 'assigned', 'contacted']);

        return $this->state(function (array $attributes) use ($agent, $action) {
            return [
                'agent_id' => $agent->id,
                'description' => ucfirst($action).' agent: '.$agent->name,
                'event' => 'Agent',
            ];
        });
    }

    /**
     * Indicate that the activity is a system activity (no user).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function system()
    {
        $action = $this->faker->randomElement(['maintenance performed', 'backup completed', 'error occurred', 'automated task completed']);

        return $this->state(function (array $attributes) use ($action) {
            return [
                'user_id' => null,
                'description' => 'System '.$action,
                'event' => 'System',
            ];
        });
    }

    /**
     * Indicate that the activity is an account activity.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function account()
    {
        $account = Account::inRandomOrder()->first() ?? Account::factory()->create();
        $action = $this->faker->randomElement(['updated settings', 'changed subscription', 'updated billing info', 'added payment method']);

        return $this->state(function (array $attributes) use ($account, $action) {
            return [
                'account_id' => $account->id,
                'description' => 'Account '.$account->name.' '.$action,
                'event' => 'Account',
            ];
        });
    }
}
