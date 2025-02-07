<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Tenant;
use App\Models\Order;
use Illuminate\Database\Seeder;

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

        // Create owner for first account
        $firstOwner = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        $firstAccount->users()->attach($firstOwner, ['is_owner' => true]);

        // Create 5 additional users for first account
        $firstAccountUsers = User::factory()->count(5)->create();
        foreach ($firstAccountUsers as $user) {
            $firstAccount->users()->attach($user, ['is_owner' => false]);
        }

        // Create second account and its users
        $secondAccount = Account::create([
            'name' => 'Second Property Management',
        ]);

        // Create owner for second account
        $secondOwner = User::factory()->create([
            'first_name' => 'Second',
            'last_name' => 'Owner',
            'email' => 'second.owner@example.com',
        ]);
        $secondAccount->users()->attach($secondOwner, ['is_owner' => true]);

        // Create 5 additional users for second account
        $secondAccountUsers = User::factory()->count(5)->create();
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
