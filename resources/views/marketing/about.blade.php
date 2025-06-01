@extends('layouts.marketing')

@section('title', 'About Oregon Past Due Rent | Our Story')
@section('description', 'Learn about the story behind Oregon Past Due Rent and how our founder\'s 20+ years of landlord
experience led to creating this essential service for Oregon property owners.')

@section('content')
<!-- Hero Section -->
<div class="relative bg-indigo-800 overflow-hidden">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover"
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1973&q=80"
            alt="Oregon apartment building">
        <div class="absolute inset-0 bg-indigo-800 opacity-75"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            About Oregon Past Due Rent
        </h1>
        <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
            Born from real landlord experience and a passion for simplifying property management in Oregon.
        </p>
    </div>
</div>

<!-- Main Content Section -->
<div class="py-16 bg-white sm:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Our Story</h2>

            <p class="text-xl text-gray-600 mb-6">
                Oregon Past Due Rent was born out of frustration — a frustration that many Oregon landlords know all too
                well.
            </p>

            <p class="text-lg text-gray-600 mb-6">
                My name is Spicer Matthews, and I'm the owner and founder of OregonPastDueRent.com. For over 20 years,
                I've been a landlord in Oregon, managing properties and navigating the ever-changing landscape of
                landlord-tenant laws. Through good times and challenging ones, I've experienced firsthand the
                complexities of property management in our state.
            </p>

            <p class="text-lg text-gray-600 mb-6">
                The recent changes in Oregon Landlord-Tenant laws as of March 2023 marked a turning point. The need and
                importance of issuing past due rent notices became more critical than ever before. What was already a
                time-consuming process became even more complex, with stricter requirements and less room for error.
            </p>

            <div class="bg-indigo-50 border-l-4 border-indigo-600 p-6 my-8">
                <p class="text-lg text-gray-700 italic">
                    "It's extremely time-consuming to build and serve these notices correctly. It's extremely risky if
                    you don't do it right — your eviction case could be thrown out on a technicality."
                </p>
                <p class="text-base text-gray-600 mt-2">— Spicer Matthews, Founder</p>
            </div>

            <p class="text-lg text-gray-600 mb-6 mt-4">
                After countless hours spent creating notices, double-checking legal requirements, and coordinating
                proper service methods, I realized there had to be a better way. Not just for me, but for all Oregon
                landlords facing the same challenges.
            </p>

            <p class="text-lg text-gray-600 mb-6">
                That's why I created OregonPastDueRent.com — to provide a simple, reliable, and legally compliant
                solution for issuing past due rent notices in Oregon. Our service combines:
            </p>

            <ul class="list-disc pl-6 mb-6 text-lg text-gray-600">
                <li class="mb-2">Deep understanding of Oregon's specific landlord-tenant laws</li>
                <li class="mb-2">Professional notice preparation and delivery</li>
                <li class="mb-2">Proper documentation for legal proceedings</li>
                <li class="mb-2">Affordable pricing without subscription fees</li>
                <li class="mb-2">Quick turnaround to meet critical deadlines</li>
            </ul>

            <p class="text-lg text-gray-600 mb-6">
                Every feature of our service has been designed with the Oregon landlord in mind, addressing the real
                challenges we face every day. We stay current with all legal changes and requirements, so you don't have
                to worry about compliance issues.
            </p>

            <p class="text-lg text-gray-600">
                Whether you're managing a single rental property or a large portfolio, OregonPastDueRent.com is here to
                make the notice process simple, efficient, and worry-free. Because as a fellow landlord, I understand
                that your time is valuable, and peace of mind is priceless.
            </p>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900">Our Mission</h2>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <p class="text-xl text-gray-700 text-center">
                To empower Oregon landlords with simple, reliable, and legally compliant tools for managing past due
                rent situations,
                saving them time and protecting their investments while ensuring fair treatment for all parties
                involved.
            </p>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900">Our Values</h2>
            <p class="mt-4 text-xl text-gray-600">
                The principles that guide everything we do
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Legal Compliance</h3>
                <p class="mt-2 text-gray-600">
                    We stay current with Oregon law to ensure every notice we prepare meets all legal requirements.
                </p>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Efficiency</h3>
                <p class="mt-2 text-gray-600">
                    We value your time and work to make the notice process as quick and simple as possible.
                </p>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Fairness</h3>
                <p class="mt-2 text-gray-600">
                    We believe in fair treatment for both landlords and tenants within the framework of Oregon law.
                </p>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Support</h3>
                <p class="mt-2 text-gray-600">
                    We're here to help you navigate the notice process with confidence and clarity.
                </p>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Reliability</h3>
                <p class="mt-2 text-gray-600">
                    Count on us for consistent, professional service every time you need to issue a notice.
                </p>
            </div>

            <div class="text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Privacy</h3>
                <p class="mt-2 text-gray-600">
                    We protect your data and your tenants' information with industry-standard security measures.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-indigo-800">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            <span class="block">Ready to simplify your notice process?</span>
            <span class="block text-indigo-200">Join fellow Oregon landlords who trust our service.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    Get Started Today
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('marketing.contact') }}"
                    class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</div>
@endsection