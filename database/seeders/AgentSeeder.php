<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test accounts
        $accounts = Account::all()->take(3);

        if ($accounts->count() >= 1) {
            // First account should have one agent
            Agent::factory()->create([
                'account_id' => $accounts[0]->id,
                'name' => 'John Smith',
                'address_1' => '123 Main Street',
                'address_2' => 'Suite 101',
                'city' => 'Portland',
                'state' => 'OR',
                'zip' => '97201',
                'phone' => '(503) 555-1234',
                'email' => 'john.smith@example.com',
            ]);
        }

        if ($accounts->count() >= 2) {
            // Second account should have two agents
            Agent::factory()->create([
                'account_id' => $accounts[1]->id,
                'name' => 'Jane Doe',
                'address_1' => '456 Broadway',
                'address_2' => null,
                'city' => 'Seattle',
                'state' => 'WA',
                'zip' => '98101',
                'phone' => '(206) 555-5678',
                'email' => 'jane.doe@example.com',
            ]);

            Agent::factory()->create([
                'account_id' => $accounts[1]->id,
                'name' => 'Robert Johnson',
                'address_1' => '789 Park Avenue',
                'address_2' => 'Apt 303',
                'city' => 'Bellevue',
                'state' => 'WA',
                'zip' => '98004',
                'phone' => '(425) 555-9012',
                'email' => 'robert.johnson@example.com',
            ]);
        }

        // Third account should have no agents (so we do nothing for it)
    }
}
