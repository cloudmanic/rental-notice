@extends('layouts.marketing')

@section('title', 'How It Works - Oregon Past Due Rent')
@section('description', 'Learn how Oregon Past Due Rent helps landlords issue legally compliant past-due rent notices in 4 simple steps. From creation to delivery, we handle everything.')

@section('content')
<!-- Hero Section -->
<section class="bg-indigo-700 py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            How It Works
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">
            Our simple 4-step process makes issuing legally compliant past-due rent notices quick and hassle-free.
        </p>
    </div>
</section>

<!-- Process Overview -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">Our Process</h2>
            <p class="mt-1 text-3xl font-extrabold text-gray-900 sm:text-4xl sm:tracking-tight">
                Simple, Fast, and Reliable
            </p>
            <p class="max-w-xl mt-5 mx-auto text-xl text-gray-500">
                From notice creation to professional delivery, we handle everything to ensure your notices are legally compliant and properly documented.
            </p>
        </div>

        <div class="mt-12">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
                <div class="relative mb-12 lg:mb-0">
                    <div class="aspect-w-16 aspect-h-9 rounded-lg shadow-lg overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="Landlord using laptop" class="w-full h-full object-center object-cover">
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                            <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="relative">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                        Taking the hassle out of past-due rent notices
                    </h3>
                    <p class="mt-3 text-lg text-gray-500">
                        Oregon's landlord-tenant laws are complex and constantly changing. A single error in your rent notice can invalidate the entire process and cost you valuable time and money. Our service ensures your notices are legally sound every time.
                    </p>

                    <dl class="mt-10 space-y-10">
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Legal Compliance</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                All notices are reviewed for compliance with current Oregon landlord-tenant laws.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Time-Saving</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Create and send notices in minutes instead of hours, eliminating paperwork and travel time.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Professional Service</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500">
                                Third-party service eliminates tenant claims of improper notice delivery.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Steps Section -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Our 4-Step Process
            </h2>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">
                From creating your notice to providing proof of service, we handle everything to make the process simple and effective.
            </p>
        </div>

        <div class="mt-16">
            <div class="relative">
                <!-- Steps connection line -->
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>

                <!-- Step indicators -->
                <div class="relative flex justify-between">
                    <div>
                        <span class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-white font-bold">1</span>
                        </span>
                    </div>
                    <div>
                        <span class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-white font-bold">2</span>
                        </span>
                    </div>
                    <div>
                        <span class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-white font-bold">3</span>
                        </span>
                    </div>
                    <div>
                        <span class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-white font-bold">4</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-50 rounded-md mb-4">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-medium text-gray-900">Create Your Notice</h3>
                        <p class="mt-4 text-base text-gray-500">
                            Enter tenant information and rent details through our simple online form. We'll automatically calculate notice periods according to Oregon law.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-50 rounded-md mb-4">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-medium text-gray-900">Legal Review</h3>
                        <p class="mt-4 text-base text-gray-500">
                            Our system automatically generates a legally compliant notice using our court-tested templates that include all required language and formatting.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-50 rounded-md mb-4">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-medium text-gray-900">Professional Service</h3>
                        <p class="mt-4 text-base text-gray-500">
                            Our professional agents deliver the notice according to Oregon law, ensuring proper service through posting, personal delivery, or certified mail.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-50 rounded-md mb-4">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-medium text-gray-900">Documentation</h3>
                        <p class="mt-4 text-base text-gray-500">
                            You receive complete proof of service documentation with details of service method, date, time, and location - ready for court if needed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Process Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-gray-900">
                The Details Matter
            </h2>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">
                Oregon's landlord-tenant laws have specific requirements for past-due rent notices. Here's how we ensure compliance at every step.
            </p>
        </div>

        <div class="space-y-16">
            <!-- Step 1 Detail -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                        1. Creating Your Notice
                    </h3>
                    <p class="mt-3 text-lg text-gray-500">
                        Our simple form captures all the information needed for a legally compliant notice:
                    </p>

                    <dl class="mt-10 space-y-4">
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Tenant Information</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Name, rental unit details, and contact information.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Rent Details</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Amount owed, payment period, and payment history.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Notice Type</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                We help determine the appropriate notice period (72-hour, 144-hour, 10-day) based on your situation.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Service Instructions</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Property access details and tenant schedules to help with delivery.
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="mt-10 lg:mt-0">
                    <div class="aspect-w-5 aspect-h-3 overflow-hidden rounded-lg shadow-lg">
                        <img src="https://images.unsplash.com/photo-1606857521015-7f9fcf423740?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="Person filling out a form on a laptop" class="object-cover">
                    </div>
                </div>
            </div>

            <!-- Step 2 Detail -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="mt-10 lg:mt-0 order-first lg:order-last">
                    <div class="aspect-w-5 aspect-h-3 overflow-hidden rounded-lg shadow-lg">
                        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff3140?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1011&q=80" alt="Legal document with signature" class="object-cover">
                    </div>
                </div>
                <div class="relative">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                        2. Legal Review & Processing
                    </h3>
                    <p class="mt-3 text-lg text-gray-500">
                        Our automated system ensures every notice meets Oregon's strict legal requirements:
                    </p>

                    <dl class="mt-10 space-y-4">
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Up-to-Date Templates</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Our templates are regularly updated to comply with all changes to Oregon landlord-tenant laws.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Required Language</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                All notices include mandatory language about tenant rights, rental assistance resources, and other legally required elements.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Proper Calculations</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Our system automatically calculates the correct notice periods and timelines according to current Oregon law.
                            </dd>
                        </div>

                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-8 w-8 rounded-md bg-indigo-500 text-white">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p class="ml-12 text-lg font-medium text-gray-900">Quality Control</p>
                            </dt>
                            <dd class="mt-1 ml-12 text-base text-gray-500">
                                Each notice undergoes automated compliance checks to ensure all required elements are present and accurate.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Third-party service and documentation sections would continue in the same pattern -->
        </div>
    </div>
</section>

<!-- Pricing Preview -->
<section class="bg-indigo-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Simple, transparent pricing</span>
            <span class="block text-indigo-200">Just $15 per notice, with no subscription required</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('marketing.pricing') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    View Pricing Details
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-600">
                    Get Started Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Frequently Asked Questions
            </h2>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">
                Have more questions about our process? Here are some common questions about our service.
            </p>
        </div>
        <div class="mt-12 max-w-3xl mx-auto divide-y-2 divide-gray-200">
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-900">
                    How quickly can you deliver a notice?
                </h3>
                <p class="mt-3 text-base text-gray-500">
                    Most notices are processed within 24 hours of submission. Delivery typically occurs within 1-2 business days after processing, depending on location and tenant availability.
                </p>
            </div>
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-900">
                    What happens if the tenant isn't home?
                </h3>
                <p class="mt-3 text-base text-gray-500">
                    Oregon law allows for several methods of service, including posting at the main entrance of the dwelling unit. Our agents follow all legally compliant methods of service and document the exact method used.
                </p>
            </div>
            <div class="py-6">
                <h3 class="text-lg font-medium text-gray-900">
                    What documentation will I receive?
                </h3>
                <p class="mt-3 text-base text-gray-500">
                    You'll receive a complete proof of service document that includes the date, time, method, and location of service, along with a copy of the notice that was served. This documentation is designed to meet Oregon court requirements.
                </p>
            </div>
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('marketing.faq') }}" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                View all frequently asked questions <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            <span class="block">Ready to get started?</span>
            <span class="block text-indigo-600">Create your first notice today.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Create an Account
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('marketing.contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>
@endsection