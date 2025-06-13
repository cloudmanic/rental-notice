@props([
    'title' => 'What Our Customers Say',
    'subtitle' => 'Trusted by Oregon Landlords'
])

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-marketing.section-header 
            subtitle="{{ $title }}"
            title="{{ $subtitle }}"
        />

        <div class="mt-12 grid gap-8 lg:grid-cols-3">
            <x-marketing.testimonial-card 
                testimonial="This service has saved me so much time. The notices are always correct and delivered quickly. Worth every penny!"
                name="Sarah Johnson"
                title="Property Manager, Portland"
            />

            <x-marketing.testimonial-card 
                testimonial="Finally, a service that understands Oregon law. The automatic date calculations alone make this invaluable."
                name="Michael Chen"
                title="Landlord, Eugene"
            />

            <x-marketing.testimonial-card 
                testimonial="Professional service at a great price. The proof of mailing documentation has been helpful in court."
                name="Lisa Rodriguez"
                title="Property Owner, Salem"
            />
        </div>
    </div>
</section>