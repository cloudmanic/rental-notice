@extends('layouts.marketing')

@section('title', 'Sitemap | Oregon Past Due Rent')
@section('description', 'Navigate all pages on Oregon Past Due Rent - your resource for legally compliant rent notices in Oregon.')

@section('content')
<!-- Hero Section -->
<div class="relative bg-indigo-800 overflow-hidden">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover"
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1973&q=80"
            alt="Oregon apartment building">
        <div class="absolute inset-0 bg-indigo-800 opacity-75"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
            Sitemap
        </h1>
        <p class="mt-4 text-xl text-indigo-100">
            Explore all pages on Oregon Past Due Rent
        </p>
    </div>
</div>

<!-- Sitemap Content -->
<div class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main Pages -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Main Pages</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('marketing.home') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Home
                    </a>
                    <p class="text-gray-600 mt-1">Learn about our Oregon past due rent notice service</p>
                </li>
                <li>
                    <a href="{{ route('marketing.how-it-works') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        How It Works
                    </a>
                    <p class="text-gray-600 mt-1">Understand our simple 3-step process</p>
                </li>
                <li>
                    <a href="{{ route('marketing.pricing') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Pricing
                    </a>
                    <p class="text-gray-600 mt-1">View our transparent pricing and bulk discounts</p>
                </li>
                <li>
                    <a href="{{ route('marketing.about') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        About Us
                    </a>
                    <p class="text-gray-600 mt-1">Learn about our story and mission</p>
                </li>
            </ul>
        </div>

        <!-- Support Pages -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Support & Information</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('marketing.faq') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Frequently Asked Questions
                    </a>
                    <p class="text-gray-600 mt-1">Find answers to common questions</p>
                </li>
                <li>
                    <a href="{{ route('marketing.contact') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Contact Us
                    </a>
                    <p class="text-gray-600 mt-1">Get in touch with our support team</p>
                </li>
            </ul>
        </div>

        <!-- Account Pages -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Account Access</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('login') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Login
                    </a>
                    <p class="text-gray-600 mt-1">Access your account dashboard</p>
                </li>
                <li>
                    <a href="{{ route('register') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Register
                    </a>
                    <p class="text-gray-600 mt-1">Create a new account to get started</p>
                </li>
                <li>
                    <a href="{{ route('password.request') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Forgot Password
                    </a>
                    <p class="text-gray-600 mt-1">Reset your account password</p>
                </li>
            </ul>
        </div>

        <!-- Legal Pages -->
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Legal & Policies</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('marketing.privacy-policy') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Privacy Policy
                    </a>
                    <p class="text-gray-600 mt-1">How we handle and protect your data</p>
                </li>
                <li>
                    <a href="{{ route('marketing.terms') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Terms of Service
                    </a>
                    <p class="text-gray-600 mt-1">Our terms and conditions of use</p>
                </li>
                <li>
                    <a href="{{ route('marketing.refund-policy') }}" class="text-lg text-indigo-600 hover:text-indigo-500">
                        Refund Policy
                    </a>
                    <p class="text-gray-600 mt-1">Our refund and cancellation policy</p>
                </li>
            </ul>
        </div>

        <!-- XML Sitemap Link -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-gray-600">
                Looking for the XML sitemap for search engines? 
                <a href="/sitemap.xml" class="text-indigo-600 hover:text-indigo-500">View XML Sitemap</a>
            </p>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            <span class="block">Can't find what you're looking for?</span>
            <span class="block text-indigo-600 text-2xl">We're here to help.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('marketing.contact') }}"
                    class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection