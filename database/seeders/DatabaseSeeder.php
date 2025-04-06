<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\Notice;
use App\Models\Agent;
use App\Models\NoticeType;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NoticeTypeSeeder::class,
        ]);

        // Create first account and its users
        $firstAccount = Account::create([
            'name' => 'Test Property Management',
            'notice_type_plan_date' => Carbon::parse('2025-01-01'),
        ]);

        // Create owner for first account with known password
        $firstOwner = User::factory()->create([
            'first_name' => 'Spicer',
            'last_name' => 'Matthews',
            'email' => 'spicer@cloudmanic.com',
            'password' => Hash::make('foobar'), // Set a known password
        ]);
        $firstAccount->users()->attach($firstOwner, ['is_owner' => true]);

        // Create 5 additional users for first account with known password
        $firstAccountUsers = User::factory()
            ->count(5)
            ->state(function (array $attributes) {
                return ['password' => Hash::make('password123')];
            })
            ->create();
        foreach ($firstAccountUsers as $user) {
            $firstAccount->users()->attach($user, ['is_owner' => false]);
        }

        // Create second account and its users
        $secondAccount = Account::create([
            'name' => 'Second Property Management',
        ]);

        // Create owner for second account with known password
        $secondOwner = User::factory()->create([
            'first_name' => 'Second',
            'last_name' => 'Owner',
            'email' => 'second.owner@example.com',
            'password' => Hash::make('password123'), // Set a known password
        ]);
        $secondAccount->users()->attach($secondOwner, ['is_owner' => true]);

        // Create 5 additional users for second account with known password
        $secondAccountUsers = User::factory()
            ->count(5)
            ->state(function (array $attributes) {
                return ['password' => Hash::make('password123')];
            })
            ->create();
        foreach ($secondAccountUsers as $user) {
            $secondAccount->users()->attach($user, ['is_owner' => false]);
        }

        // Create third account and its users
        $thirdAccount = Account::create([
            'name' => 'Third Property Management',
        ]);

        // Create owner for third account with known password
        $thirdOwner = User::factory()->create([
            'first_name' => 'Third',
            'last_name' => 'Owner',
            'email' => 'third.owner@example.com',
            'password' => Hash::make('password123'), // Set a known password
        ]);
        $thirdAccount->users()->attach($thirdOwner, ['is_owner' => true]);

        // Create 3 additional users for third account with known password
        $thirdAccountUsers = User::factory()
            ->count(3)
            ->state(function (array $attributes) {
                return ['password' => Hash::make('password123')];
            })
            ->create();
        foreach ($thirdAccountUsers as $user) {
            $thirdAccount->users()->attach($user, ['is_owner' => false]);
        }

        // Create tenants and notices for first account
        $firstAccountTenants = Tenant::factory()
            ->count(10)
            ->create([
                'account_id' => $firstAccount->id,
            ]);

        // Create some tenants and notices for second account too
        $secondAccountTenants = Tenant::factory()
            ->count(5)
            ->create([
                'account_id' => $secondAccount->id,
            ]);

        // Create some tenants and notices for third account
        $thirdAccountTenants = Tenant::factory()
            ->count(7)
            ->create([
                'account_id' => $thirdAccount->id,
            ]);

        // Create agents for each account using the AgentSeeder
        $this->call([
            AgentSeeder::class,
        ]);

        // Get all agents
        $agents = [
            1 => Agent::where('account_id', $firstAccount->id)->get(),
            2 => Agent::where('account_id', $secondAccount->id)->get(),
            3 => Agent::where('account_id', $thirdAccount->id)->get(),
        ];

        // Get users by account
        $users = [
            1 => $firstAccount->users,
            2 => $secondAccount->users,
            3 => $thirdAccount->users,
        ];

        // Get tenants by account
        $tenants = [
            1 => $firstAccountTenants,
            2 => $secondAccountTenants,
            3 => $thirdAccountTenants,
        ];

        // Get notice types
        $noticeTypes = NoticeType::all();

        // Generate 200 notices spread across the accounts
        // First account: 100 notices
        // Second account: 60 notices
        // Third account: 40 notices
        $noticeDistribution = [
            1 => 100, // First account
            2 => 60,  // Second account
            3 => 40,  // Third account
        ];

        foreach ($noticeDistribution as $accountId => $count) {
            $account = $accountId == 1 ? $firstAccount : ($accountId == 2 ? $secondAccount : $thirdAccount);

            // Skip if this account has no agents
            if ($agents[$accountId]->isEmpty()) {
                continue;
            }

            // Create notices for this account
            for ($i = 0; $i < $count; $i++) {
                $user = $users[$accountId]->random();
                $tenant = $tenants[$accountId]->random();
                $agent = $agents[$accountId]->random();
                $noticeType = $noticeTypes->random();

                Notice::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                    'agent_id' => $agent->id,
                    'notice_type_id' => $noticeType->id,
                    'price' => $noticeType->price,
                    'past_due_rent' => fake()->randomFloat(2, 500, 5000),
                    'late_charges' => fake()->randomFloat(2, 0, 500),
                    'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }

        // Call ActivitySeeder after creating notices
        $this->call([
            ActivitySeeder::class,
        ]);
    }
}
