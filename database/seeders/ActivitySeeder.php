<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the accounts that were created in DatabaseSeeder
        $accounts = Account::all();

        // Get all users
        $users = User::all();

        // Create activities with dates spread across the last year
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();

        // Create 50 tenant activities
        Activity::factory()
            ->count(50)
            ->tenant()
            ->state(function (array $attributes) use ($startDate, $endDate, $accounts, $users) {
                return [
                    'account_id' => $accounts->random()->id,
                    'user_id' => $users->random()->id,
                    'created_at' => Carbon::parse($startDate)->addSeconds(rand(0, $endDate->diffInSeconds($startDate))),
                ];
            })
            ->create();

        // Create 50 notice activities
        Activity::factory()
            ->count(50)
            ->notice()
            ->state(function (array $attributes) use ($startDate, $endDate, $users) {
                return [
                    'user_id' => $users->random()->id,
                    'created_at' => Carbon::parse($startDate)->addSeconds(rand(0, $endDate->diffInSeconds($startDate))),
                ];
            })
            ->create();

        // Create 50 agent activities
        Activity::factory()
            ->count(50)
            ->agent()
            ->state(function (array $attributes) use ($startDate, $endDate, $accounts, $users) {
                return [
                    'account_id' => $accounts->random()->id,
                    'user_id' => $users->random()->id,
                    'created_at' => Carbon::parse($startDate)->addSeconds(rand(0, $endDate->diffInSeconds($startDate))),
                ];
            })
            ->create();

        // Create 25 system activities
        Activity::factory()
            ->count(25)
            ->system()
            ->state(function (array $attributes) use ($startDate, $endDate, $accounts) {
                return [
                    'account_id' => $accounts->random()->id,
                    'created_at' => Carbon::parse($startDate)->addSeconds(rand(0, $endDate->diffInSeconds($startDate))),
                ];
            })
            ->create();

        // Create 25 account activities
        Activity::factory()
            ->count(25)
            ->account()
            ->state(function (array $attributes) use ($startDate, $endDate, $users) {
                return [
                    'user_id' => $users->random()->id,
                    'created_at' => Carbon::parse($startDate)->addSeconds(rand(0, $endDate->diffInSeconds($startDate))),
                ];
            })
            ->create();
    }
}
