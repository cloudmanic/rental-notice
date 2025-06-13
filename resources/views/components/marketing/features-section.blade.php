@props([
    'title' => 'Why Oregon Landlords Choose Us',
    'subtitle' => 'Everything You Need to Serve Legal Notices',
    'description' => null,
    'showPricing' => false,
    'price' => null,
    'originalPrice' => null
])

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-marketing.section-header 
            subtitle="{{ $title }}"
            title="{{ $subtitle }}"
            :description="$description"
        />

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <x-marketing.feature-card 
                    title="100% Oregon Compliant"
                    description="Our notices meet all Oregon legal requirements including proper formatting, required language, and timing specifications.">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>

                <x-marketing.feature-card 
                    title="Same-Day Service"
                    description="Submit your notice before 2 PM PT and we'll mail it the same business day with tracking included.">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>

                <x-marketing.feature-card 
                    title="{{ $showPricing ? 'Fixed Price: $' . number_format($price, 2) : 'Affordable Flat Rate' }}"
                    description="{{ $showPricing ? ($originalPrice ? '<span class=\'line-through text-gray-400\'>$' . number_format($originalPrice, 2) . '</span> per notice includes everything: printing, mailing, tracking, and certificate of mailing.' : '$' . number_format($price, 2) . ' per notice includes everything: printing, mailing, tracking, and certificate of mailing.') : 'Just $' . ($price ? number_format($price, 2) : '15') . ' per notice with no subscription fees. Only pay when you need to send a notice.' }}">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>

                <x-marketing.feature-card 
                    title="Professional Documentation"
                    description="Receive a complete package with your notice, proof of mailing, and certificate of mailing for your records.">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>

                <x-marketing.feature-card 
                    title="Multiple Tenants Support"
                    description="Easily include all tenants on a single notice and we'll ensure proper delivery to each one.">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>

                <x-marketing.feature-card 
                    title="Automatic Date Calculations"
                    description="We calculate all deadline dates correctly, accounting for weekends and holidays per Oregon law.">
                    <x-slot name="icon">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </x-slot>
                </x-marketing.feature-card>
            </div>
        </div>
    </div>
</section>