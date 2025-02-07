<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\Order;
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

        // Create tenants and orders for first account
        Tenant::factory()
            ->count(10)
            ->create([
                'account_id' => $firstAccount->id,
            ]);

        // Create orders for first account
        Order::factory()
            ->count(5)
            ->create([
                'account_id' => $firstAccount->id,
                'user_id' => $firstOwner->id,
                'notice_type_id' => 1,
                'agent_name' => $firstOwner->full_name,
                'agent_email' => $firstOwner->email,
            ]);

        Order::factory()
            ->count(5)
            ->create([
                'account_id' => $firstAccount->id,
                'user_id' => $firstOwner->id,
                'notice_type_id' => 2,
                'agent_name' => $firstOwner->full_name,
                'agent_email' => $firstOwner->email,
            ]);

        // Create some tenants and orders for second account too
        Tenant::factory()
            ->count(5)
            ->create([
                'account_id' => $secondAccount->id,
            ]);

        Order::factory()
            ->count(3)
            ->create([
                'account_id' => $secondAccount->id,
                'user_id' => $secondOwner->id,
                'notice_type_id' => 1,
                'agent_name' => $secondOwner->full_name,
                'agent_email' => $secondOwner->email,
            ]);
    }
}
