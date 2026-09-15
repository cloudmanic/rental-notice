{{--
    Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
    Date: 2026-09-15

    Cloudflare Turnstile widget for public forms. Renders nothing when the
    Turnstile keys are not set.
--}}
@if (App\Rules\Turnstile::enabled())
<div {{ $attributes }}>
    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
    @error(App\Rules\Turnstile::FIELD)
    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
(function () {
    // Render the widget ourselves: after a wire:navigate page change Cloudflare's
    // script is already loaded and will not scan the new page on its own.
    window.renderTurnstileWidgets = function () {
        document.querySelectorAll('.cf-turnstile:not([data-turnstile-rendered])').forEach(function (el) {
            el.setAttribute('data-turnstile-rendered', '');
            window.turnstile.render(el);
        });
    };

    if (window.turnstile) {
        window.renderTurnstileWidgets();
    } else if (!document.getElementById('turnstile-api')) {
        var script = document.createElement('script');
        script.id = 'turnstile-api';
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=renderTurnstileWidgets';
        script.async = true;
        document.head.appendChild(script);
    }
})();
</script>
@endif
