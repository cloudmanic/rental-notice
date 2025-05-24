<?php

namespace Tests\Feature\Accounts;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountsAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected $account;

    protected $superAdminUser;

    protected $adminUser;

    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test account
        $this->account = Account::factory()->create([
            'name' => 'Test Company',
        ]);

        // Create a Super Admin user
        $this->superAdminUser = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super.admin@example.com',
            'password' => Hash::make('password123'),
            'type' => User::TYPE_SUPER_ADMIN,
        ]);

        // Create an Admin user
        $this->adminUser = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'type' => User::TYPE_ADMIN,
        ]);

        // Create a Regular user
        $this->regularUser = User::factory()->create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'regular@example.com',
            'password' => Hash::make('password123'),
            'type' => User::TYPE_CONTRIBUTOR,
        ]);

        // Attach users to the account
        $this->account->users()->attach($this->superAdminUser);
        $this->account->users()->attach($this->adminUser);
        $this->account->users()->attach($this->regularUser, ['is_owner' => true]);

        // Set the current account for each user
        $this->superAdminUser->account = $this->account;
        $this->adminUser->account = $this->account;
        $this->regularUser->account = $this->account;
    }

    #[Test]
    public function super_admin_can_access_accounts_page()
    {
        $response = $this->actingAs($this->superAdminUser)
            ->get(route('accounts.index'));

        $response->assertStatus(200);
        $response->assertSee('Accounts');
        $response->assertDontSee('403');
    }

    #[Test]
    public function admin_user_cannot_access_accounts_page()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('accounts.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function regular_user_cannot_access_accounts_page()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('accounts.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_accounts_page()
    {
        $response = $this->get(route('accounts.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function accounts_link_appears_in_navigation_for_super_admin()
    {
        $response = $this->actingAs($this->superAdminUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('accounts.index'));
    }

    #[Test]
    public function accounts_link_does_not_appear_in_navigation_for_admin()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee(route('accounts.index'));
    }

    #[Test]
    public function accounts_link_does_not_appear_in_navigation_for_regular_user()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee(route('accounts.index'));
    }
}
