<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\Notice;
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
        Tenant::factory()
            ->count(10)
            ->create([
                'account_id' => $firstAccount->id,
            ]);

        // Create notices for first account
        Notice::factory()
            ->count(5)
            ->create([
                'account_id' => $firstAccount->id,
                'user_id' => $firstOwner->id,
                'notice_type_id' => 1,
                'agent_name' => $firstOwner->full_name,
                'agent_email' => $firstOwner->email,
            ]);

        Notice::factory()
            ->count(5)
            ->create([
                'account_id' => $firstAccount->id,
                'user_id' => $firstOwner->id,
                'notice_type_id' => 2,
                'agent_name' => $firstOwner->full_name,
                'agent_email' => $firstOwner->email,
            ]);

        // Create some tenants and notices for second account too
        Tenant::factory()
            ->count(5)
            ->create([
                'account_id' => $secondAccount->id,
            ]);

        Notice::factory()
            ->count(3)
            ->create([
                'account_id' => $secondAccount->id,
                'user_id' => $secondOwner->id,
                'notice_type_id' => 1,
                'agent_name' => $secondOwner->full_name,
                'agent_email' => $secondOwner->email,
            ]);

        // Create some tenants and notices for third account
        Tenant::factory()
            ->count(7)
            ->create([
                'account_id' => $thirdAccount->id,
            ]);

        Notice::factory()
            ->count(4)
            ->create([
                'account_id' => $thirdAccount->id,
                'user_id' => $thirdOwner->id,
                'notice_type_id' => 3,
                'agent_name' => $thirdOwner->full_name,
                'agent_email' => $thirdOwner->email,
            ]);

        // Seed agents for the accounts
        $this->call([
            AgentSeeder::class,
            ActivitySeeder::class, // Add the ActivitySeeder
        ]);
    }
}
