<?php

namespace Tests\Feature\Account;

use App\Livewire\Account\Edit;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and user for testing
        $this->account = Account::factory()->create([
            'name' => 'Feature Test Company'
        ]);

        $this->user = User::factory()->create([
            'first_name' => 'Feature',
            'last_name' => 'Tester',
            'email' => 'feature.test@example.com',
            'password' => Hash::make('password123')
        ]);

        $this->account->users()->attach($this->user, ['is_owner' => true]);

        // Set the user's current account
        $this->user->account = $this->account;
    }

    #[Test]
    public function authenticated_user_can_access_account_edit_page()
    {
        $response = $this->actingAs($this->user)->get(route('account.edit'));

        $response->assertStatus(200);
        $response->assertSee('Account Settings');
        $response->assertSee('Delete Account');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_account_edit_page()
    {
        $response = $this->get(route('account.edit'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function account_settings_link_appears_in_navigation_menu()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('account.edit'));
    }

    #[Test]
    public function user_can_update_account_name_through_form()
    {
        // Use Livewire test to simulate the component interaction
        $this->actingAs($this->user);

        Livewire::test(Edit::class)
            ->set('name', 'Updated Feature Company')
            ->call('update')
            ->assertHasNoErrors();

        // Verify the account name was updated in the database
        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'name' => 'Updated Feature Company'
        ]);
    }
}
