<?php

/*
 * Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
 * Date: 2026-09-15
 */

namespace Tests\Unit\Rules;

use App\Rules\Turnstile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TurnstileTest extends TestCase
{
    /**
     * Turn Turnstile on with fake keys for each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
    }

    /**
     * Run the Turnstile rule against the given form data.
     *
     * @param  array  $data  The submitted form fields
     */
    protected function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, [Turnstile::FIELD => [new Turnstile]]);
    }

    #[Test]
    public function it_is_enabled_only_when_both_keys_are_set()
    {
        $this->assertTrue(Turnstile::enabled());

        config(['services.turnstile.secret_key' => null]);
        $this->assertFalse(Turnstile::enabled());

        config(['services.turnstile.secret_key' => 'test-secret-key', 'services.turnstile.site_key' => '']);
        $this->assertFalse(Turnstile::enabled());
    }

    #[Test]
    public function it_passes_without_calling_cloudflare_when_disabled()
    {
        config(['services.turnstile.secret_key' => null]);
        Http::fake();

        $this->assertTrue($this->validate([])->passes());
        Http::assertNothingSent();
    }

    #[Test]
    public function it_fails_when_the_token_is_missing()
    {
        Http::fake();

        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has(Turnstile::FIELD));
        Http::assertNothingSent();
    }

    #[Test]
    public function it_passes_when_cloudflare_accepts_the_token()
    {
        Http::fake([Turnstile::VERIFY_URL => Http::response(['success' => true])]);

        $this->assertTrue($this->validate([Turnstile::FIELD => 'good-token'])->passes());

        Http::assertSent(function (Request $request) {
            return $request->url() === Turnstile::VERIFY_URL
                && $request['secret'] === 'test-secret-key'
                && $request['response'] === 'good-token';
        });
    }

    #[Test]
    public function it_fails_when_cloudflare_rejects_the_token()
    {
        Http::fake([Turnstile::VERIFY_URL => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ])]);

        $this->assertTrue($this->validate([Turnstile::FIELD => 'bad-token'])->fails());
    }

    #[Test]
    public function it_passes_when_cloudflare_cannot_be_reached()
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timed out');
        });

        $this->assertTrue($this->validate([Turnstile::FIELD => 'any-token'])->passes());
    }
}
