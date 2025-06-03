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

        // Verify users are deleted since they are no longer associated with any accounts
        $this->assertDatabaseMissing('users', ['id' => $owner->id]);
        $this->assertDatabaseMissing('users', ['id' => $member->id]);

        // Verify super admin user still exists (should never be deleted)
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    /**
     * Test that users associated with multiple accounts are not deleted
     */
    public function test_users_with_multiple_accounts_are_not_deleted()
    {
        // Create a super admin user
        $superAdmin = User::factory()->create(['type' => 'Super Admin']);

        // Create two accounts
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        // Create a user associated with both accounts
        $sharedUser = User::factory()->create();
        $account1->users()->attach($sharedUser, ['is_owner' => true]);
        $account2->users()->attach($sharedUser, ['is_owner' => false]);

        // Create a user only associated with account1
        $singleAccountUser = User::factory()->create();
        $account1->users()->attach($singleAccountUser, ['is_owner' => false]);

        // Verify users exist and their associations
        $this->assertDatabaseHas('users', ['id' => $sharedUser->id]);
        $this->assertDatabaseHas('users', ['id' => $singleAccountUser->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account1->id, 'user_id' => $sharedUser->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account2->id, 'user_id' => $sharedUser->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account1->id, 'user_id' => $singleAccountUser->id]);

        // Delete account1
        Livewire::actingAs($superAdmin)
            ->test(\App\Livewire\Accounts\Index::class)
            ->call('deleteAccount', $account1->id);

        // Verify account1 is deleted
        $this->assertDatabaseMissing('accounts', ['id' => $account1->id]);
        $this->assertDatabaseMissing('account_to_user', ['account_id' => $account1->id]);

        // Verify shared user still exists (has association with account2)
        $this->assertDatabaseHas('users', ['id' => $sharedUser->id]);
        $this->assertDatabaseHas('account_to_user', ['account_id' => $account2->id, 'user_id' => $sharedUser->id]);

        // Verify single account user is deleted (no longer associated with any accounts)
        $this->assertDatabaseMissing('users', ['id' => $singleAccountUser->id]);

        // Verify account2 still exists
        $this->assertDatabaseHas('accounts', ['id' => $account2->id]);
    }

    /**
     * Test that super admin users are never deleted
     */
    public function test_super_admin_users_are_never_deleted()
    {
        // Create two super admin users
        $superAdmin1 = User::factory()->create(['type' => 'Super Admin']);
        $superAdmin2 = User::factory()->create(['type' => 'Super Admin']);

        // Create an account and associate superAdmin2 with it
        $account = Account::factory()->create();
        $account->users()->attach($superAdmin2, ['is_owner' => true]);

        // Verify users exist
        $this->assertDatabaseHas('users', ['id' => $superAdmin1->id]);
        $this->assertDatabaseHas('users', ['id' => $superAdmin2->id]);

        // Delete the account
        Livewire::actingAs($superAdmin1)
            ->test(\App\Livewire\Accounts\Index::class)
            ->call('deleteAccount', $account->id);

        // Verify account is deleted
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);

        // Verify both super admin users still exist (super admins should never be deleted)
        $this->assertDatabaseHas('users', ['id' => $superAdmin1->id]);
        $this->assertDatabaseHas('users', ['id' => $superAdmin2->id]);
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
