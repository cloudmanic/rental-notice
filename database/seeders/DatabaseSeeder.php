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
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Begin transaction to ensure we can rollback if needed
        DB::beginTransaction();

        try {
            $this->call([
                NoticeTypeSeeder::class,
            ]);

            // Create first account or find if it exists
            $firstAccount = Account::firstOrCreate(
                ['name' => 'Test Property Management'],
                ['notice_type_plan_date' => Carbon::parse('2025-01-01')]
            );

            // Create owner for first account with known password or find if exists
            $firstOwnerEmail = 'spicer@cloudmanic.com';
            $firstOwner = User::firstOrCreate(
                ['email' => $firstOwnerEmail],
                [
                    'first_name' => 'Spicer',
                    'last_name' => 'Matthews',
                    'password' => Hash::make('foobar'), // Set a known password
                    'email_verified_at' => now(),
                ]
            );

            // Attach user to account if not already attached
            if (!$firstAccount->users()->where('user_id', $firstOwner->id)->exists()) {
                $firstAccount->users()->attach($firstOwner, ['is_owner' => true]);
            }

            // Create 5 additional users for first account with known password
            for ($i = 0; $i < 5; $i++) {
                $user = User::factory()->create([
                    'password' => Hash::make('password123'),
                    'email' => 'first_account_user_' . ($i + 1) . '@example.com',
                ]);
                if (!$firstAccount->users()->where('user_id', $user->id)->exists()) {
                    $firstAccount->users()->attach($user, ['is_owner' => false]);
                }
            }

            // Create second account or find if it exists
            $secondAccount = Account::firstOrCreate(
                ['name' => 'Second Property Management'],
                ['notice_type_plan_date' => NoticeType::getMostRecentPlanDate()]
            );

            // Create owner for second account with known password or find if exists
            $secondOwnerEmail = 'second.owner@example.com';
            $secondOwner = User::firstOrCreate(
                ['email' => $secondOwnerEmail],
                [
                    'first_name' => 'Second',
                    'last_name' => 'Owner',
                    'password' => Hash::make('password123'), // Set a known password
                    'email_verified_at' => now(),
                ]
            );

            // Attach user to account if not already attached
            if (!$secondAccount->users()->where('user_id', $secondOwner->id)->exists()) {
                $secondAccount->users()->attach($secondOwner, ['is_owner' => true]);
            }

            // Create 5 additional users for second account with known password
            for ($i = 0; $i < 5; $i++) {
                $user = User::factory()->create([
                    'password' => Hash::make('password123'),
                    'email' => 'second_account_user_' . ($i + 1) . '@example.com',
                ]);
                if (!$secondAccount->users()->where('user_id', $user->id)->exists()) {
                    $secondAccount->users()->attach($user, ['is_owner' => false]);
                }
            }

            // Create third account or find if it exists
            $thirdAccount = Account::firstOrCreate(
                ['name' => 'Third Property Management'],
                ['notice_type_plan_date' => NoticeType::getMostRecentPlanDate()]
            );

            // Create owner for third account with known password or find if exists
            $thirdOwnerEmail = 'third.owner@example.com';
            $thirdOwner = User::firstOrCreate(
                ['email' => $thirdOwnerEmail],
                [
                    'first_name' => 'Third',
                    'last_name' => 'Owner',
                    'password' => Hash::make('password123'), // Set a known password
                    'email_verified_at' => now(),
                ]
            );

            // Attach user to account if not already attached
            if (!$thirdAccount->users()->where('user_id', $thirdOwner->id)->exists()) {
                $thirdAccount->users()->attach($thirdOwner, ['is_owner' => true]);
            }

            // Create 3 additional users for third account with known password
            for ($i = 0; $i < 3; $i++) {
                $user = User::factory()->create([
                    'password' => Hash::make('password123'),
                    'email' => 'third_account_user_' . ($i + 1) . '@example.com',
                ]);
                if (!$thirdAccount->users()->where('user_id', $user->id)->exists()) {
                    $thirdAccount->users()->attach($user, ['is_owner' => false]);
                }
            }

            // Create tenants for each account if they don't exist yet
            $firstAccountTenants = $this->createTenantsForAccount($firstAccount, 10);
            $secondAccountTenants = $this->createTenantsForAccount($secondAccount, 5);
            $thirdAccountTenants = $this->createTenantsForAccount($thirdAccount, 7);

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

            // If notice types are available, proceed with creating notices
            if ($noticeTypes->isNotEmpty()) {
                // Clear existing notices if desired (optional, commented out by default)
                // Notice::truncate();

                // Generate notices spread across the accounts
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

                    // Check if account already has enough notices
                    $existingNoticesCount = Notice::where('account_id', $account->id)->count();
                    if ($existingNoticesCount >= $count) {
                        continue; // Skip if we already have enough notices
                    }

                    // Create notices for this account
                    for ($i = 0; $i < ($count - $existingNoticesCount); $i++) {
                        $user = $users[$accountId]->random();
                        $tenant = $tenants[$accountId]->random();
                        $agent = $agents[$accountId]->random();
                        $noticeType = $noticeTypes->random();

                        $notice = Notice::factory()->create([
                            'account_id' => $account->id,
                            'user_id' => $user->id,
                            'agent_id' => $agent->id,
                            'notice_type_id' => $noticeType->id,
                            'price' => $noticeType->price,
                            'past_due_rent' => fake()->randomFloat(2, 500, 5000),
                            'late_charges' => fake()->randomFloat(2, 0, 500),
                            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                        ]);

                        // Attach the tenant to the notice without is_primary flag
                        $notice->tenants()->attach($tenant->id);

                        // Randomly attach additional tenants (0-2 more)
                        $additionalTenants = $tenants[$accountId]->except($tenant->id)->random(rand(0, 2));
                        foreach ($additionalTenants as $additionalTenant) {
                            $notice->tenants()->attach($additionalTenant->id);
                        }
                    }
                }
            }

            // Call ActivitySeeder after creating notices
            $this->call([
                ActivitySeeder::class,
            ]);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create tenants for an account
     */
    private function createTenantsForAccount(Account $account, int $count): \Illuminate\Database\Eloquent\Collection
    {
        // Get existing tenants for this account
        $existingTenants = Tenant::where('account_id', $account->id)->get();

        // If we already have enough tenants, return them
        if ($existingTenants->count() >= $count) {
            return $existingTenants->take($count);
        }

        // Otherwise, create the additional needed tenants
        $neededCount = $count - $existingTenants->count();
        $newTenants = Tenant::factory()
            ->count($neededCount)
            ->create([
                'account_id' => $account->id,
            ]);

        // Combine existing and new tenants
        return $existingTenants->concat($newTenants);
    }
}
