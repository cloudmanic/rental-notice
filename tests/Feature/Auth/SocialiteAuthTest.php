<?php

namespace Tests\Feature\Auth;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialiteAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_redirect_to_google_oauth(): void
    {
        $response = $this->get(route('auth.social.redirect', 'google'));

        $response->assertRedirect();
    }

    public function test_user_can_redirect_to_apple_oauth(): void
    {
        $response = $this->get(route('auth.social.redirect', 'apple'));

        $response->assertRedirect();
    }

    public function test_invalid_provider_returns_404(): void
    {
        $response = $this->get(route('auth.social.redirect', 'invalid'));

        $response->assertNotFound();
    }

    public function test_new_user_can_register_via_google(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock([
                'user' => $socialiteUser,
            ]));

        $response = $this->get(route('auth.social.callback', 'google'));

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'google_id' => 'google123',
            'google_avatar' => 'https://avatar.url',
            'type' => 'Admin',
        ]);

        $this->assertDatabaseHas('accounts', [
            'name' => 'John\'s Account',
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_new_user_can_register_via_apple(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('apple123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Smith');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://apple-avatar.url');

        Socialite::shouldReceive('driver')
            ->with('apple')
            ->andReturn(Mockery::mock([
                'user' => $socialiteUser,
            ]));

        $response = $this->get(route('auth.social.callback', 'apple'));

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'apple_id' => 'apple123',
            'apple_avatar' => 'https://apple-avatar.url',
        ]);

        $this->assertTrue(Auth::check());
    }

    public function test_existing_user_can_login_via_google(): void
    {
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
        ]);

        // Attach user to account
        $user->accounts()->attach($account->id, ['is_owner' => true]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google456');
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Existing User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://new-avatar.url');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock([
                'user' => $socialiteUser,
            ]));

        $response = $this->get(route('auth.social.callback', 'google'));

        $response->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertEquals('google456', $user->google_id);
        $this->assertEquals('https://new-avatar.url', $user->google_avatar);
        $this->assertTrue(Auth::check());
    }

    public function test_existing_user_with_social_login_can_login(): void
    {
        $account = Account::factory()->create();
        $user = User::factory()->create([
            'email' => 'social@example.com',
            'google_id' => 'existing_google_id',
            'google_avatar' => 'https://existing-avatar.url',
        ]);

        // Attach user to account
        $user->accounts()->attach($account->id, ['is_owner' => true]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('existing_google_id');
        $socialiteUser->shouldReceive('getEmail')->andReturn('social@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Social User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://existing-avatar.url');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock([
                'user' => $socialiteUser,
            ]));

        $response = $this->get(route('auth.social.callback', 'google'));

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_socialite_error_redirects_to_login_with_error(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new \Exception('OAuth error'));

        $response = $this->get(route('auth.social.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['social']);
    }

    public function test_user_model_has_social_login_methods(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google123',
            'apple_id' => null,
            'google_avatar' => 'https://google-avatar.url',
        ]);

        $this->assertTrue($user->hasSocialLogin('google'));
        $this->assertFalse($user->hasSocialLogin('apple'));
        $this->assertEquals('https://google-avatar.url', $user->avatar);
    }

    public function test_account_name_extraction_from_full_name(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google789');
        $socialiteUser->shouldReceive('getEmail')->andReturn('john.smith@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Michael Smith');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock([
                'user' => $socialiteUser,
            ]));

        $response = $this->get(route('auth.social.callback', 'google'));

        $this->assertDatabaseHas('accounts', [
            'name' => 'John\'s Account',
        ]);
    }
}
