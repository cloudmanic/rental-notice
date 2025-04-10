<?php

namespace Tests\Unit\Livewire\Profile;

use App\Livewire\Profile\Edit;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class EditTest extends TestCase
{
    use RefreshDatabase;

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
    public function it_initializes_with_correct_user_data()
    {
        Livewire::test(Edit::class)
            ->assertSet('first_name', 'John')
            ->assertSet('last_name', 'Doe')
            ->assertSet('email', 'john@example.com');
    }

    #[Test]
    public function it_can_update_profile_information()
    {
        Livewire::test(Edit::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('email', 'jane@example.com')
            ->call('updateProfile');

        // Reload user from database
        $this->user->refresh();

        // Assert the user data was updated
        $this->assertEquals('Jane', $this->user->first_name);
        $this->assertEquals('Smith', $this->user->last_name);
        $this->assertEquals('jane@example.com', $this->user->email);
    }

    #[Test]
    public function it_validates_required_profile_fields()
    {
        Livewire::test(Edit::class)
            ->set('first_name', '')
            ->set('last_name', '')
            ->set('email', '')
            ->call('updateProfile')
            ->assertHasErrors(['first_name', 'last_name', 'email']);
    }

    #[Test]
    public function it_validates_email_format()
    {
        Livewire::test(Edit::class)
            ->set('email', 'not-an-email')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function it_can_update_password_when_current_password_is_correct()
    {
        Livewire::test(Edit::class)
            ->set('current_password', 'password123')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword');

        // Reload user from database
        $this->user->refresh();

        // Assert the password was updated
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    #[Test]
    public function it_requires_correct_current_password()
    {
        Livewire::test(Edit::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors('current_password');
    }

    #[Test]
    public function it_validates_password_confirmation()
    {
        Livewire::test(Edit::class)
            ->set('current_password', 'password123')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors('password');
    }

    #[Test]
    public function it_validates_minimum_password_length()
    {
        Livewire::test(Edit::class)
            ->set('current_password', 'password123')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors('password');
    }
}
