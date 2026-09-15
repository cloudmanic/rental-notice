<?php

/*
 * Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
 * Date: 2026-09-15
 */

namespace Tests\Feature\Http\Controllers;

use App\Mail\ContactFormSubmission;
use App\Rules\Turnstile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A valid contact form submission.
     */
    protected function contactForm(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Landlord',
            'email' => 'jane@example.com',
            'subject' => 'Question about pricing',
            'message' => 'How much is a 10-day notice?',
        ], $overrides);
    }

    /**
     * Turn Turnstile on with fake keys.
     */
    protected function enableTurnstile(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
    }

    #[Test]
    public function contact_form_sends_an_email_when_turnstile_is_disabled()
    {
        Mail::fake();

        $response = $this->post(route('marketing.contact.send'), $this->contactForm());

        $response->assertRedirect(route('marketing.contact'));
        Mail::assertSent(ContactFormSubmission::class);
    }

    #[Test]
    public function contact_form_is_blocked_when_the_turnstile_token_is_missing()
    {
        Mail::fake();
        Http::fake();
        $this->enableTurnstile();

        $response = $this->post(route('marketing.contact.send'), $this->contactForm());

        $response->assertSessionHasErrors([Turnstile::FIELD]);
        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_form_is_blocked_when_the_turnstile_check_fails()
    {
        Mail::fake();
        Http::fake([Turnstile::VERIFY_URL => Http::response(['success' => false])]);
        $this->enableTurnstile();

        $response = $this->post(route('marketing.contact.send'), $this->contactForm([
            Turnstile::FIELD => 'bad-token',
        ]));

        $response->assertSessionHasErrors([Turnstile::FIELD]);
        Mail::assertNothingSent();
    }

    #[Test]
    public function contact_form_sends_an_email_without_the_token_when_the_turnstile_check_passes()
    {
        Mail::fake();
        Http::fake([Turnstile::VERIFY_URL => Http::response(['success' => true])]);
        $this->enableTurnstile();

        $response = $this->post(route('marketing.contact.send'), $this->contactForm([
            Turnstile::FIELD => 'good-token',
        ]));

        $response->assertRedirect(route('marketing.contact'));
        Mail::assertSent(ContactFormSubmission::class, function ($mail) {
            return ! array_key_exists(Turnstile::FIELD, $mail->formData);
        });
    }

    #[Test]
    public function contact_page_shows_the_turnstile_widget_when_enabled()
    {
        $this->enableTurnstile();

        $this->get(route('marketing.contact'))
            ->assertSee('class="cf-turnstile"', false)
            ->assertSee('data-sitekey="test-site-key"', false);
    }
}
