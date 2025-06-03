<?php

namespace Tests\Feature\Accounts;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that super admin can delete an account and all related data
     */
    public function test_super_admin_can_delete_account_with_all_related_data()
    {
        // Seed notice types
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);

        // Create a super admin user
        $superAdmin = User::factory()->create(['type' => 'Super Admin']);

        // Create an account with related data
        $account = Account::factory()->create();

        // Create users associated with the account
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $account->users()->attach($owner, ['is_owner' => true]);
        $account->users()->attach($member, ['is_owner' => false]);

        // Create related data
        $tenant1 = Tenant::factory()->create(['account_id' => $account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $account->id]);

        $agent = Agent::factory()->create(['account_id' => $account->id]);

        $notice1 = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'agent_id' => $agent->id,
        ]);
        $notice2 = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'agent_id' => $agent->id,
        ]);

        // Attach tenants to notices
        $notice1->tenants()->attach($tenant1);
        $notice2->tenants()->attach([$tenant1->id, $tenant2->id]);

        // Create activities
        Activity::factory()->count(3)->create(['account_id' => $account->id]);

        // Verify data exists
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account->id, 'user_id' => $owner->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account->id, 'user_id' => $member->id]);
        $this->assertDatabaseHas('tenants', ['account_id' => $account->id]);
        $this->assertDatabaseHas('agents', ['account_id' => $account->id]);
        $this->assertDatabaseHas('notices', ['account_id' => $account->id]);
        $this->assertDatabaseHas('notice_tenant', ['notice_id' => $notice1->id]);
        $this->assertDatabaseHas('activities', ['account_id' => $account->id]);

        // Act as super admin and delete the account
        Livewire::actingAs($superAdmin)
            ->test(\App\Livewire\Accounts\Index::class)
            ->call('deleteAccount', $account->id);

        // Verify account and all related data is deleted
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
        $this->assertDatabaseMissing('account_to_user', ['account_id' => $account->id]);
        $this->assertDatabaseMissing('tenants', ['account_id' => $account->id]);
        $this->assertDatabaseMissing('agents', ['account_id' => $account->id]);
        $this->assertDatabaseMissing('notices', ['account_id' => $account->id]);
        $this->assertDatabaseMissing('notice_tenant', ['notice_id' => $notice1->id]);
        $this->assertDatabaseMissing('notice_tenant', ['notice_id' => $notice2->id]);
        $this->assertDatabaseMissing('activities', ['account_id' => $account->id]);

        // Verify users still exist (they should not be deleted)
        $this->assertDatabaseHas('users', ['id' => $owner->id]);
        $this->assertDatabaseHas('users', ['id' => $member->id]);
    }

    /**
     * Test that regular users cannot access the accounts page
     */
    public function test_regular_user_cannot_access_accounts_page()
    {
        $user = User::factory()->create(['type' => 'Contributor']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Accounts\Index::class)
            ->assertForbidden();
    }
}
