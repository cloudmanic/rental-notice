<?php

namespace Tests\Feature\Auth;

use App\Models\Account;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
            'is_owner' => 1,
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

    /**
     * Test that users are assigned the Admin type by default when registering.
     */
    #[Test]
    public function test_new_users_are_assigned_admin_type_by_default(): void
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Test Company',
        ];

        // Submit the registration form
        $response = $this->post(route('register'), $userData);

        // Assert the response redirects to dashboard
        $response->assertRedirect(route('dashboard'));

        // Assert that a user was created with the given email
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'type' => User::TYPE_ADMIN,
        ]);

        // Assert that the user is an admin
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($user->isAdmin());
    }

    #[Test]
    public function it_sends_notification_on_successful_registration()
    {
        // Queue is already faked in TestCase setUp, which prevents actual sending
        Notification::fake();

        $response = $this->post(route('register'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Test Company',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'john.doe@example.com')->first();

        // Assert that the notification was sent
        // With Notification::fake(), this verifies the notification would be sent
        // but doesn't actually send it
        Notification::assertSentTo(
            $user,
            UserRegistered::class,
            function ($notification) use ($user) {
                return $notification->user->id === $user->id &&
                       $notification->accountName === 'Test Company';
            }
        );
    }

    #[Test]
    public function no_notifications_sent_when_queue_is_faked()
    {
        // This test verifies that with Queue::fake() in TestCase,
        // no actual notifications are sent even with a running queue worker

        $response = $this->post(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Test Company',
        ]);

        $response->assertRedirect(route('dashboard'));

        // Since Queue is faked, the notification job should be pushed but not processed
        Queue::assertPushed(\Illuminate\Notifications\SendQueuedNotifications::class);
    }
}
