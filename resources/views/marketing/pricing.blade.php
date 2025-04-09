@extends('layouts.marketing')

@section('title', 'Pricing - Oregon Past Due Rent')
@section('description', 'Simple, transparent pricing for Oregon Past Due Rent notices. Just $15 per notice with no subscriptions or hidden fees.')

@section('content')
<!-- Hero Section -->
<section class="bg-indigo-700 py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            Simple, Transparent Pricing
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">
            No subscriptions. No hidden fees. Just pay for what you use.
        </p>
    </div>
</section>

<!-- Main Pricing Section -->
<section class="py-16 bg-white sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-5">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Pay only for what you need, when you need it
                </h2>
                <p class="mt-4 text-lg text-gray-500">
                    We believe in fair, straightforward pricing. No monthly subscriptions that make you pay even when you don't use the service.
                </p>
                <div class="mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">No Monthly Fees</h3>
                            <p class="mt-1 text-base text-gray-500">
                                Unlike other services that charge ongoing fees, we only charge when you create a notice.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">No Setup Fees</h3>
                            <p class="mt-1 text-base text-gray-500">
                                Creating an account is completely free. You only pay when you send a notice.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">No Hidden Costs</h3>
                            <p class="mt-1 text-base text-gray-500">
                                Our $15 fee includes notice creation, delivery, and standard proof of service.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 lg:mt-0 lg:col-span-7">
                <div class="rounded-lg bg-gray-50 shadow-lg overflow-hidden">
                    <div class="px-6 py-8 sm:p-10 sm:pb-6">
                        <div>
                            <h3 class="inline-flex px-4 py-1 rounded-full text-sm font-semibold tracking-wide uppercase bg-indigo-100 text-indigo-600">
                                Standard Notice
                            </h3>
                        </div>
                        <div class="mt-4 flex items-baseline text-6xl font-extrabold">
                            $15
                            <span class="ml-1 text-2xl font-medium text-gray-500">per notice</span>
                        </div>
                        <p class="mt-5 text-lg text-gray-500">
                            Everything you need to issue a legally compliant notice.
                        </p>
                    </div>
                    <div class="px-6 pt-6 pb-8 bg-gray-50 sm:p-10 sm:pt-6">
                        <ul role="list" class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">
                                    Court-tested notice template for 10-day or 13-day notices
                                </p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">
                                    Professional third-party service
                                </p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">
                                    Compliant delivery via personal service, posting, and mail
                                </p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">
                                    Electronic confirmation and proof of service
                                </p>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-3 text-base text-gray-700">
                                    Secure online record storage
                                </p>
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" class="block w-full bg-indigo-600 border border-transparent rounded-md py-3 px-5 text-center font-medium text-white hover:bg-indigo-700">
                                Get started today
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bulk Pricing Section -->
<section class="bg-indigo-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Volume Pricing for Property Managers
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Special rates available for property management companies that serve multiple notices per month.
            </p>
        </div>

        <div class="mt-12 bg-white rounded-lg shadow-lg overflow-hidden lg:grid lg:grid-cols-2 lg:gap-0">
            <div class="p-8 sm:p-10">
                <h3 class="text-2xl font-extrabold text-gray-900">
                    Bulk Notices
                </h3>
                <p class="mt-4 text-lg text-gray-500">
                    Managing multiple properties? We offer volume discounts for property managers who send 10+ notices per month.
                </p>
                <ul role="list" class="mt-8 space-y-5 lg:space-y-0 lg:grid lg:grid-cols-1 lg:gap-x-8 lg:gap-y-5">
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-700">
                            Dedicated account management
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-700">
                            Volume-based pricing
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-700">
                            Enhanced reporting and analytics
                        </p>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-base text-gray-700">
                            Priority service and support
                        </p>
                    </li>
                </ul>
            </div>
            <div class="p-8 sm:p-10 bg-gray-50">
                <h3 class="text-2xl font-extrabold text-gray-900">
                    Contact us for custom pricing
                </h3>
                <p class="mt-4 text-lg text-gray-500">
                    Fill out our quick form to receive a custom quote based on your volume needs.
                </p>
                <div class="mt-8">
                    <div class="rounded-md shadow">
                        <a href="{{ route('marketing.contact') }}?subject=Bulk%20Pricing%20Inquiry" class="flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Request Custom Quote
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Comparison Section -->
<section class="py-16 bg-white sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                The Cost of Doing It Yourself
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Our service is more affordable than you might think when you consider the alternatives.
            </p>
        </div>

        <div class="mt-16 bg-white rounded-lg overflow-hidden divide-y divide-gray-200 lg:divide-y-0 lg:divide-x">
            <div class="lg:grid lg:grid-cols-3">
                <!-- DIY Approach Column -->
                <div class="p-8 sm:p-10">
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-900">DIY Approach</h3>
                        <span class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            High Risk
                        </span>
                    </div>
                    <p class="mt-4 text-base text-gray-500">Handling notices yourself might seem less expensive initially, but consider these hidden costs:</p>
                    <ul role="list" class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$100-250+</strong> in legal research to ensure compliance
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>4-8 hours</strong> of your time creating and delivering notices
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$500+</strong> in possible court fees if notices are rejected
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$1,000s</strong> in lost rent due to delays and technicalities
                            </p>
                        </li>
                    </ul>
                </div>

                <!-- Hiring a Lawyer Column -->
                <div class="p-8 sm:p-10 border-t border-gray-200 lg:border-t-0">
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-900">Hiring a Lawyer</h3>
                        <span class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Expensive
                        </span>
                    </div>
                    <p class="mt-4 text-base text-gray-500">Having an attorney handle your notices is secure but expensive:</p>
                    <ul role="list" class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$250-500</strong> for a single notice preparation
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$100-200</strong> additional for service coordination
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>Days or weeks</strong> of waiting for lawyer availability
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>$1,500+</strong> if you need representation for eviction
                            </p>
                        </li>
                    </ul>
                </div>

                <!-- Oregon Past Due Rent Column -->
                <div class="p-8 sm:p-10 border-t border-gray-200 lg:border-t-0 bg-green-50">
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-900">Oregon Past Due Rent</h3>
                        <span class="ml-4 inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Best Value
                        </span>
                    </div>
                    <p class="mt-4 text-base text-gray-500">Our service provides all benefits at a fraction of the cost:</p>
                    <ul role="list" class="mt-8 space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>Just $15</strong> per notice, all-inclusive
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>Minutes</strong> to complete rather than hours
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>Legally compliant</strong> templates and service methods
                            </p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="ml-3 text-base text-gray-700">
                                <strong>Same-day processing</strong> to start your timeline immediately
                            </p>
                        </li>
                    </ul>
                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="block w-full bg-green-600 border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-green-700">
                            Get started now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto divide-y-2 divide-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl text-center">
                Frequently Asked Questions
            </h2>
            <dl class="mt-10 space-y-6 divide-y divide-gray-200">
                <div class="pt-6">
                    <dt class="text-lg">
                        <button class="text-left w-full flex justify-between items-start text-gray-900 focus:outline-none">
                            <span class="font-medium">Are there any additional fees beyond the $15 per notice?</span>
                        </button>
                    </dt>
                    <dd class="mt-2 pr-12">
                        <p class="text-base text-gray-500">
                            No, the $15 fee covers everything you need - notice creation, delivery via all required methods, and standard proof of service. The only potential additional cost would be for optional notarized affidavits, which can be requested for a small additional fee.
                        </p>
                    </dd>
                </div>
                <div class="pt-6">
                    <dt class="text-lg">
                        <button class="text-left w-full flex justify-between items-start text-gray-900 focus:outline-none">
                            <span class="font-medium">Do I need to sign up for a subscription?</span>
                        </button>
                    </dt>
                    <dd class="mt-2 pr-12">
                        <p class="text-base text-gray-500">
                            No, there's no subscription required. You only pay when you create and send a notice. Your account remains active for whenever you need it, with no monthly or annual fees.
                        </p>
                    </dd>
                </div>
                <div class="pt-6">
                    <dt class="text-lg">
                        <button class="text-left w-full flex justify-between items-start text-gray-900 focus:outline-none">
                            <span class="font-medium">What payment methods do you accept?</span>
                        </button>
                    </dt>
                    <dd class="mt-2 pr-12">
                        <p class="text-base text-gray-500">
                            We accept all major credit cards and debit cards. For property management companies with bulk notice needs, we can also arrange for invoicing.
                        </p>
                    </dd>
                </div>
                <div class="pt-6">
                    <dt class="text-lg">
                        <button class="text-left w-full flex justify-between items-start text-gray-900 focus:outline-none">
                            <span class="font-medium">How do I qualify for bulk pricing?</span>
                        </button>
                    </dt>
                    <dd class="mt-2 pr-12">
                        <p class="text-base text-gray-500">
                            If you anticipate sending 10 or more notices per month, you may qualify for our bulk pricing program. Please contact us through the form on this page, and we'll provide a custom quote based on your specific needs and volume.
                        </p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Ready to simplify your past-due rent notices?</h2>
            <p class="mt-4 text-lg text-gray-500">
                Join Oregon landlords who are saving time and money while ensuring legal compliance.
            </p>
            <div class="mt-8 flex justify-center">
                <div class="inline-flex rounded-md shadow">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Create your free account
                    </a>
                </div>
                <div class="ml-3 inline-flex">
                    <a href="{{ route('marketing.contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        Contact us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection