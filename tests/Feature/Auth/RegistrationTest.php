<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_user_can_register_with_valid_information()
    {
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Test Company',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertDatabaseHas('accounts', [
            'name' => 'Test Company',
        ]);

        $user = User::where('email', 'john.doe@example.com')->first();
        $account = Account::where('name', 'Test Company')->first();

        $this->assertDatabaseHas('account_to_user', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'is_owner' => 1
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function company_name_is_optional_when_registering()
    {
        $response = $this->post(route('register'), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@example.com',
        ]);

        $user = User::where('email', 'jane.doe@example.com')->first();
        $expectedAccountName = "Jane Doe's Company";

        $this->assertDatabaseHas('accounts', [
            'name' => $expectedAccountName,
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function user_cannot_register_with_invalid_information()
    {
        // Missing required fields
        $response = $this->post(route('register'), [
            'first_name' => '',
            'last_name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'not-matching',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'password']);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('accounts', 0);
    }

    #[Test]
    public function user_cannot_register_with_duplicate_email()
    {
        // Create a user first
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);

        // Try to register with the same email
        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 1); // Still just the one user
    }
}
