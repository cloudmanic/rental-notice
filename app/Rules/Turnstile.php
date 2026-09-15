<?php

/*
 * Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
 * Date: 2026-09-15
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    /**
     * Cloudflare's endpoint for checking a widget token.
     */
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * The form field the Turnstile widget posts its token in.
     */
    const FIELD = 'cf-turnstile-response';

    /**
     * Run this rule even when the token is missing, so a bot that skips the
     * widget still fails.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Determine if Turnstile is turned on.
     *
     * Both keys must be set. Local dev and tests have no keys, so the forms
     * work there without Cloudflare.
     */
    public static function enabled(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    /**
     * Ask Cloudflare if the widget token is real and fail validation if not.
     *
     * @param  string  $attribute  The name of the field being validated
     * @param  mixed  $value  The token the widget put in the form
     * @param  \Closure  $fail  Callback to mark the field as invalid
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::enabled()) {
            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('Please confirm you are human and try again.');

            return;
        }

        try {
            $response = Http::asForm()->timeout(10)->post(self::VERIFY_URL, [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);
        } catch (ConnectionException $e) {
            // Don't lock real people out of the form when Cloudflare can't be reached.
            Log::warning('Turnstile verification skipped: '.$e->getMessage());

            return;
        }

        if ($response->json('success') !== true) {
            $fail('Please confirm you are human and try again.');
        }
    }
}
