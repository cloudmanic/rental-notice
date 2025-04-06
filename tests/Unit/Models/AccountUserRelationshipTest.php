<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AccountUserRelationshipTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function account_can_have_many_users()
    {
        $account = Account::factory()->create([
            'name' => 'Test Property Management'
        ]);

        $owner = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Attach users to account
        $account->users()->attach($owner, ['is_owner' => true]);
        $account->users()->attach([$user1->id, $user2->id], ['is_owner' => false]);

        // Refresh the account model to load the relationships
        $account = $account->fresh('users');

        $this->assertCount(3, $account->users);
        $this->assertTrue($account->users->contains($owner));
        $this->assertTrue($account->users->contains($user1));
        $this->assertTrue($account->users->contains($user2));
    }

    #[Test]
    public function user_can_belong_to_many_accounts()
    {
        $user = User::factory()->create();

        $account1 = Account::factory()->create(['name' => 'First Property']);
        $account2 = Account::factory()->create(['name' => 'Second Property']);

        // Attach accounts to user
        $user->accounts()->attach($account1, ['is_owner' => true]);
        $user->accounts()->attach($account2, ['is_owner' => false]);

        // Refresh the user model to load the relationships
        $user = $user->fresh('accounts');

        $this->assertCount(2, $user->accounts);
        $this->assertTrue($user->accounts->contains($account1));
        $this->assertTrue($user->accounts->contains($account2));
    }

    #[Test]
    public function pivot_table_tracks_owner_status_correctly()
    {
        $account = Account::factory()->create();
        $owner = User::factory()->create();
        $regularUser = User::factory()->create();

        $account->users()->attach($owner, ['is_owner' => true]);
        $account->users()->attach($regularUser, ['is_owner' => false]);

        $pivotOwner = $account->users()->where('users.id', $owner->id)->first()->pivot;
        $pivotRegular = $account->users()->where('users.id', $regularUser->id)->first()->pivot;

        $this->assertEquals(1, $pivotOwner->is_owner);
        $this->assertEquals(0, $pivotRegular->is_owner);
    }

    #[Test]
    public function deleting_account_detaches_users()
    {
        $account = Account::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $account->users()->attach([$user1->id, $user2->id]);

        $this->assertEquals(2, $account->users()->count());

        $account->delete();

        $this->assertEquals(0, \DB::table('account_to_user')->where('account_id', $account->id)->count());

        // Users should still exist
        $this->assertDatabaseHas('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
    }
}
