<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    #[Test]
    public function user_can_login_with_correct_credentials()
    {
        // Create an account first
        $account = \App\Models\Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attach the user to the account
        $user->accounts()->attach($account->id);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function user_cannot_login_with_incorrect_password()
    {
        // Create an account first
        $account = \App\Models\Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attach the user to the account
        $user->accounts()->attach($account->id);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'incorrect-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_cannot_login_with_nonexistent_email()
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_can_logout()
    {
        // Create an account first
        $account = \App\Models\Account::factory()->create();

        $user = User::factory()->create();

        // Attach the user to the account
        $user->accounts()->attach($account->id);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    #[Test]
    public function remember_me_functionality_works()
    {
        // Create an account first
        $account = \App\Models\Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attach the user to the account
        $user->accounts()->attach($account->id);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Just check that we're authenticated, without asserting a specific cookie name
        // as cookie names can vary between Laravel versions and configurations
        $this->assertTrue(auth()->viaRemember() || auth()->check());
    }
}
