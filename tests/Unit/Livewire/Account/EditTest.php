<?php

namespace Tests\Unit\Livewire\Account;

use App\Livewire\Account\Edit;
use App\Models\Account;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and user for testing
        $this->account = Account::factory()->create([
            'name' => 'Test Company'
        ]);

        $this->user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->account->users()->attach($this->user, ['is_owner' => true]);

        // Set the user's current account
        $this->user->account = $this->account;

        // Login the user
        $this->actingAs($this->user);

        // Set the account ID in the session
        session(['account_id' => $this->account->id]);
    }

    #[Test]
    public function it_initializes_with_correct_account_data()
    {
        Livewire::test(Edit::class)
            ->assertSet('name', 'Test Company')
            ->assertSet('showDeleteModal', false);
    }

    #[Test]
    public function it_can_update_account_name()
    {
        // Count initial activities
        $initialActivityCount = Activity::count();

        Livewire::test(Edit::class)
            ->set('name', 'Updated Company Name')
            ->call('update')
            ->assertSet('name', 'Updated Company Name');

        // Reload account from database
        $this->account->refresh();

        // Assert the account name was updated in the database
        $this->assertEquals('Updated Company Name', $this->account->name);

        // Assert activity was logged
        $this->assertEquals($initialActivityCount + 1, Activity::count());
        $this->assertDatabaseHas('activities', [
            'description' => 'Account name was updated to Updated Company Name.',
            'event' => 'Account',
            'account_id' => $this->account->id
        ]);
    }

    #[Test]
    public function it_validates_account_name_is_required()
    {
        Livewire::test(Edit::class)
            ->set('name', '')
            ->call('update')
            ->assertHasErrors(['name' => 'required']);

        // Ensure account name remains unchanged
        $this->account->refresh();
        $this->assertEquals('Test Company', $this->account->name);
    }

    #[Test]
    public function it_can_toggle_delete_modal()
    {
        Livewire::test(Edit::class)
            ->assertSet('showDeleteModal', false)
            ->call('confirmDelete')
            ->assertSet('showDeleteModal', true)
            ->call('cancelDelete')
            ->assertSet('showDeleteModal', false);
    }

    #[Test]
    public function it_can_delete_account()
    {
        $accountId = $this->account->id;

        Livewire::test(Edit::class)
            ->call('delete')
            ->assertRedirect(route('login'));

        // Assert the account was deleted
        $this->assertDatabaseMissing('accounts', [
            'id' => $accountId
        ]);

        // Assert user is not deleted (important!)
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id
        ]);
    }
}
