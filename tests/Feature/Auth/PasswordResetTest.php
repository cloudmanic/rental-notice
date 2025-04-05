<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_password_reset_request_form()
    {
        $response = $this->get(route('password.request'));

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    #[Test]
    public function user_can_request_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    #[Test]
    public function user_cannot_request_reset_for_nonexistent_email()
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Get a valid reset token directly from the password broker
        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(password_verify('new-password', $user->fresh()->password));
    }

    #[Test]
    public function user_cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $originalPassword = $user->password;

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals($originalPassword, $user->fresh()->password);
    }

    #[Test]
    public function user_cannot_reset_password_with_mismatched_passwords()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = Password::createToken($user);
        $originalPassword = $user->password;

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertEquals($originalPassword, $user->fresh()->password);
    }
}
