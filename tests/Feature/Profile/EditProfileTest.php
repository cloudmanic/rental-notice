<?php

namespace Tests\Feature\Profile;

use App\Models\Account;
use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $account;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and user for testing
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);
        $this->account->users()->attach($this->user, ['is_owner' => true]);

        // Login the user
        $this->actingAs($this->user);

        // Set the current account ID in the session
        session(['account_id' => $this->account->id]);
    }

    #[Test]
    public function profile_page_can_be_rendered()
    {
        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_update_profile_information()
    {
        // Count initial activities
        $initialActivityCount = Activity::count();

        // Send update request
        Livewire::test('profile.edit')
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'jane@example.com')
            ->call('updateProfile');

        // Reload user from database
        $this->user->refresh();

        // Assert the user data was updated in the database
        $this->assertEquals('Jane', $this->user->first_name);
        $this->assertEquals('Smith', $this->user->last_name);
        $this->assertEquals('jane@example.com', $this->user->email);

        // Assert activity was logged
        $this->assertEquals($initialActivityCount + 1, Activity::count());
        $latestActivity = Activity::latest('id')->first();
        $this->assertStringContainsString("Jane's profile information was updated", $latestActivity->description);
    }

    #[Test]
    public function user_cannot_update_profile_with_invalid_email()
    {
        // Send update request with invalid email
        Livewire::test('profile.edit')
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'not-a-valid-email')
            ->call('updateProfile')
            ->assertHasErrors(['email' => 'email']);

        // Make sure user data wasn't changed
        $this->user->refresh();
        $this->assertEquals('John', $this->user->first_name);
        $this->assertEquals('john@example.com', $this->user->email);
    }

    #[Test]
    public function user_can_update_password()
    {
        // Count initial activities
        $initialActivityCount = Activity::count();

        // Send password update request
        Livewire::test('profile.edit')
            ->set('current_password', 'password123')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword');

        // Reload user from database
        $this->user->refresh();

        // Assert the password was updated
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));

        // Assert activity was logged
        $this->assertEquals($initialActivityCount + 1, Activity::count());
        $latestActivity = Activity::latest('id')->first();
        $this->assertEquals('John\'s password was updated.', $latestActivity->description);
    }

    #[Test]
    public function user_cannot_update_password_with_invalid_current_password()
    {
        // Remember original password hash
        $originalPasswordHash = $this->user->password;

        // Send password update request with wrong current password
        Livewire::test('profile.edit')
            ->set('current_password', 'wrong-password')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors('current_password');

        // Reload user from database
        $this->user->refresh();

        // Assert the password was not updated
        $this->assertEquals($originalPasswordHash, $this->user->password);
    }

    #[Test]
    public function user_cannot_update_password_with_mismatched_confirmation()
    {
        // Remember original password hash
        $originalPasswordHash = $this->user->password;

        // Send password update request with mismatched confirmation
        Livewire::test('profile.edit')
            ->set('current_password', 'password123')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors('password');

        // Reload user from database
        $this->user->refresh();

        // Assert the password was not updated
        $this->assertEquals($originalPasswordHash, $this->user->password);
    }

    #[Test]
    public function user_cannot_update_password_with_short_password()
    {
        // Remember original password hash
        $originalPasswordHash = $this->user->password;

        // Send password update request with short password
        Livewire::test('profile.edit')
            ->set('current_password', 'password123')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors('password');

        // Reload user from database
        $this->user->refresh();

        // Assert the password was not updated
        $this->assertEquals($originalPasswordHash, $this->user->password);
    }
}
