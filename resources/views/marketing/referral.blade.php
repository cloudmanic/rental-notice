@extends('layouts.marketing')

@section('title', 'Special Offer from ' . $referrer->full_name . ' | Oregon Past Due Rent Notices')
@section('description', $referrer->full_name . ' has invited you to use Oregon Past Due Rent with a special discount. Get professionally delivered past-due rent notices for just $' . $discountedPrice . ' per notice.')

@section('content')
@php
$discountPercentage = round(($discountAmount / $standardPrice) * 100);
$possessiveName = $referrer->first_name . ' ' . $referrer->last_name . (str_ends_with($referrer->last_name, 's') ? "'" : "'s");
$possessiveFirstName = $referrer->first_name . (str_ends_with($referrer->first_name, 's') ? "'" : "'s");
$primaryButtonText = "Claim " . $possessiveFirstName . " Discount";
$ctaTitle = "<span class='block'>Ready to get started?</span><span class='block'>Claim <span class='font-bold text-indigo-300'>" . $possessiveFirstName . "</span> " . $discountPercentage . "% discount now.</span>";
$buttonText = "Sign Up With " . $possessiveFirstName . " Discount";
$discountBannerText = "<span class=\"font-bold\">" . $possessiveFirstName . "</span> special " . $discountPercentage . "% discount has been automatically applied!";
@endphp

<!-- Hero Section with Referrer Welcome -->
<x-marketing.hero 
    title="<span class='text-3xl sm:text-4xl lg:text-5xl block mb-2'>{{ $possessiveName }}</span>Special Offer: Save {{ $discountPercentage }}% <br class='hidden sm:block'>on Past Due Rent Notices"
    description="Issue legally compliant 10-day or 13-day nonpayment notices with professional delivery. Thanks to <span class='font-semibold text-indigo-200'>{{ $possessiveFirstName }}</span> referral, you'll pay just <span class='font-bold'>${{ number_format($discountedPrice, 2) }}</span> per notice instead of <span class='line-through'>${{ number_format($standardPrice, 2) }}</span>."
    primaryButtonText="{{ $primaryButtonText }}"
    :showSpecialOffer="true"
    specialOfferText="<span class='text-xl font-bold'>{{ $referrer->first_name }} {{ $referrer->last_name }}</span> <span class='text-base font-normal'>has invited you to join Oregon Past Due Rent</span>"
/>

<!-- Discount Banner -->
<div class="bg-green-50 border-b border-green-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center">
            <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-800 text-lg font-medium">
                {!! $discountBannerText !!}
            </p>
        </div>
    </div>
</div>

<!-- Video Section -->
<x-marketing.video-tutorial trackingLocation="Referral Page" />

<!-- Features Section -->
<x-marketing.features-section 
    description="<span class='font-semibold text-indigo-600'>{{ $referrer->first_name }} {{ $referrer->last_name }}</span> trusts us for their past-due rent notices. Here's why you should too."
    :showPricing="true"
    :price="$discountedPrice"
    :originalPrice="$standardPrice"
/>

<!-- How It Works Section -->
<x-marketing.how-it-works :price="$discountedPrice" />

<!-- CTA Section -->
<x-marketing.cta-section 
    title="{!! $ctaTitle !!}"
    description="Join <span class='font-semibold'>{{ $referrer->first_name }} {{ $referrer->last_name }}</span> and hundreds of other Oregon landlords who trust us for their past-due rent notices."
    buttonText="{{ $buttonText }}"
/>

<!-- Testimonial Section -->
<x-marketing.testimonials-section />
@endsection