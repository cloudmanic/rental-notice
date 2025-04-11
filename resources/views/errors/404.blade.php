@extends('layouts.marketing')

@section('title', 'Page Not Found - Oregon Past Due Rent')
@section('description', 'Sorry, we couldn\'t find the page you were looking for. Please check the URL or return to our
homepage.')

@section('content')
<!-- 404 Hero Section -->
<section class="bg-indigo-700 py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            404
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-2xl text-indigo-100 font-semibold">
            Page Not Found
        </p>
    </div>
</section>

<!-- 404 Content Section -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center">
                <svg class="h-24 w-24 text-indigo-400 mx-auto mb-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                    We couldn't find that page
                </h2>
                <p class="mt-6 text-xl text-gray-500">
                    The page you're looking for doesn't exist or may have been moved.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('marketing.home') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Return to Homepage
                    </a>
                    <a href="{{ route('marketing.contact') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Contact Support
                    </a>
                </div>
            </div>

            <div class="mt-16">
                <h3 class="text-lg font-medium text-gray-900">Popular pages you might be looking for:</h3>
                <ul class="mt-4 space-y-4 border-t border-b border-gray-200 py-6">
                    <li class="relative pl-8">
                        <svg class="absolute left-0 top-0.5 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('marketing.how-it-works') }}" class="text-gray-600 hover:text-indigo-600">How
                            Past Due Rent Notices Work</a>
                    </li>
                    <li class="relative pl-8">
                        <svg class="absolute left-0 top-0.5 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('marketing.pricing') }}" class="text-gray-600 hover:text-indigo-600">Service
                            Pricing</a>
                    </li>
                    <li class="relative pl-8">
                        <svg class="absolute left-0 top-0.5 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('marketing.faq') }}" class="text-gray-600 hover:text-indigo-600">Frequently
                            Asked Questions</a>
                    </li>
                    <li class="relative pl-8">
                        <svg class="absolute left-0 top-0.5 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-600">Create Your
                            Account</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-indigo-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8 lg:flex lg:items-center lg:justify-between">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            <span class="block">Ready to get started?</span>
            <span class="block text-indigo-600">Just ${{ App\Facades\Pricing::getStandardPrice() }} per notice with no
                subscription fees.</span>
        </h2>
        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Get Started
                </a>
            </div>
            <div class="ml-3 inline-flex rounded-md shadow">
                <a href="{{ route('marketing.pricing') }}"
                    class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                    View Pricing
                </a>
            </div>
        </div>
    </div>
</section>
@endsection