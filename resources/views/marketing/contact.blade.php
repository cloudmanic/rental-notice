@extends('layouts.marketing')

@section('title', 'Contact Us - Oregon Past Due Rent')
@section('description', 'Have questions about Oregon Past Due Rent\'s notice service? Contact our team for assistance
with past-due rent notices, pricing, or legal requirements.')

@section('content')
<!-- Hero Section -->
<section class="bg-indigo-700 py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            Contact Us
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">
            Have questions or need assistance? Our team is here to help.
        </p>
    </div>
</section>

<!-- Contact Form Section -->
<section class="relative bg-white py-16 sm:py-24">
    <div class="lg:absolute lg:inset-0">
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover lg:absolute lg:h-full"
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1773&q=80"
                alt="Modern apartment building">
        </div>
    </div>
    <div class="relative py-16 px-4 sm:py-24 sm:px-6 lg:px-8 lg:max-w-7xl lg:mx-auto lg:py-32 lg:grid lg:grid-cols-2">
        <div class="lg:pr-8">
            <div class="max-w-md mx-auto sm:max-w-lg lg:mx-0">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Get in touch
                </h2>
                <p class="mt-4 text-lg text-gray-500 sm:mt-3">
                    We'd love to hear from you! Send us a message using the form below, or contact us directly using the
                    information provided.
                </p>

                @if(session('success'))
                <div class="rounded-md bg-green-50 p-4 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('marketing.contact.send') }}" method="POST"
                    class="mt-9 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    @csrf
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" autocomplete="name"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md py-3 px-4 bg-white border-2"
                                value="{{ old('name') }}">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md py-3 px-4 bg-white border-2"
                                value="{{ old('email') }}">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <div class="mt-1">
                            <input type="text" name="subject" id="subject"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md py-3 px-4 bg-white border-2"
                                value="{{ old('subject', request('subject')) }}">
                            @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="flex justify-between">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <span id="message-max" class="text-sm text-gray-500">
                                Max. 500 characters
                            </span>
                        </div>
                        <div class="mt-1">
                            <textarea id="message" name="message" rows="4"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md py-3 px-4 bg-white border-2"
                                maxlength="500">{{ old('message') }}</textarea>
                            @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information Section -->
<section class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Additional Ways to Reach Us
            </h2>
            <p class="mt-3 text-xl text-gray-500 sm:mt-4">
                Choose the method that works best for you.
            </p>
        </div>
        <div class="mt-12 max-w-lg mx-auto grid gap-8 lg:grid-cols-3 lg:max-w-none">
            <!-- Email -->
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Email</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-base text-gray-500">
                                For general inquiries and support:
                            </p>
                            <p class="mt-2 text-base text-indigo-600 font-medium">
                                {{ config('constants.company_support_email') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phone -->
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Phone</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-base text-gray-500">
                                Monday-Friday, 9am-5pm PT:
                            </p>
                            <p class="mt-2 text-base text-indigo-600 font-medium">
                                {{ config('constants.company_phone') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Business Hours</h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-base text-gray-500">Monday - Friday:</p>
                            <p class="text-base text-gray-900">9:00 AM - 5:00 PM PT</p>
                            <p class="mt-2 text-base text-gray-500">Saturday - Sunday:</p>
                            <p class="text-base text-gray-900">Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview Section -->
<section class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Frequently Asked Questions</h2>
            <p class="mt-4 text-lg text-gray-500">
                Can't find the answer you're looking for? Check our comprehensive FAQ section.
            </p>
        </div>
        <div class="mt-12">
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                <div>
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How much does each notice cost?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Each notice costs $15. This flat fee covers the entire process from notice creation to delivery
                        and proof of service.
                    </dd>
                </div>

                <div>
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How quickly are notices delivered?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        We process notices within one business day of submission. The actual delivery timeframe depends
                        on the tenant's location, but is typically completed within 1-3 business days.
                    </dd>
                </div>

                <div>
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What's the difference between 10-day and 13-day notices?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        A 10-day notice is used for standard rental situations. A 13-day notice is required for certain
                        subsidized housing scenarios.
                    </dd>
                </div>

                <div>
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Do you offer bulk pricing?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Yes, we offer special bulk pricing for property managers who send 10 or more notices per month.
                        Please contact us for a custom quote.
                    </dd>
                </div>
            </dl>
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('marketing.faq') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                View All FAQs
            </a>
        </div>
    </div>
</section>

<!-- Newsletter Sign-up -->
<section class="bg-indigo-700">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center">
        <div class="lg:w-0 lg:flex-1">
            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Sign up for our newsletter
            </h2>
            <p class="mt-3 max-w-3xl text-lg leading-6 text-indigo-200">
                Stay updated on Oregon landlord-tenant laws and get tips for managing your rental property.
            </p>
        </div>
        <div class="mt-8 lg:mt-0 lg:ml-8">
            <form action="{{ route('marketing.newsletter.subscribe') }}" method="POST" class="sm:flex">
                @csrf
                <label for="newsletter-email" class="sr-only">Email address</label>
                <input id="newsletter-email" name="email" type="email" autocomplete="email" required
                    class="w-full px-5 py-3 border border-transparent placeholder-gray-500 focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-700 focus:ring-white focus:border-white sm:max-w-xs rounded-md"
                    placeholder="Enter your email">
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                    <button type="submit"
                        class="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                        Subscribe
                    </button>
                </div>
            </form>
            <p class="mt-3 text-sm text-indigo-200">
                We care about your data. Read our <a href="{{ route('marketing.privacy-policy') }}"
                    class="text-white font-medium underline">Privacy Policy</a>.
            </p>
        </div>
    </div>
</section>
@endsection