@props([
    'title' => 'Simple Process',
    'subtitle' => 'How It Works',
    'description' => 'Get your past-due rent notice delivered in three easy steps.',
    'showPricing' => false,
    'price' => null
])

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-marketing.section-header 
            subtitle="{{ $title }}"
            title="{{ $subtitle }}"
            description="{{ $description }}"
        />

        <div class="mt-16">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <x-marketing.process-step 
                    step="1"
                    title="Enter Tenant Info"
                    description="Fill out a simple form with tenant details and the amount owed. Takes less than 2 minutes."
                />

                <x-marketing.process-step 
                    step="2"
                    title="Review & Pay"
                    description="Preview your notice and pay securely. Just ${{ $price ? number_format($price, 2) : '15.00' }} includes everything."
                />

                <x-marketing.process-step 
                    step="3"
                    title="We Handle Delivery"
                    description="We print, mail, and track your notice. You'll receive proof of mailing for your records."
                />
            </div>
        </div>
    </div>
</section>