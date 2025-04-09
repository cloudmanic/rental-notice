<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Oregon Past Due Rent - Landlord Notice Service')</title>
    <meta name="description" content="@yield('description', 'Oregon Past Due Rent helps landlords issue legally compliant past-due rent notices with professional delivery for just $15 per notice.')">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Site Title -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('marketing.home') }}" class="flex items-center">
                            <!-- You can replace with an actual logo -->
                            <div class="h-8 w-8 bg-indigo-600 rounded-full flex items-center justify-center mr-2">
                                <span class="text-white font-bold text-lg">OR</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Oregon Past Due Rent</span>
                        </a>
                    </div>
                </div>

                <!-- Navigation Links - Desktop -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('marketing.home') }}" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('marketing.home') ? 'text-indigo-600' : 'text-gray-900' }}">
                        Home
                    </a>
                    <a href="{{ route('marketing.how-it-works') }}" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('marketing.how-it-works') ? 'text-indigo-600' : 'text-gray-900' }}">
                        How It Works
                    </a>
                    <a href="{{ route('marketing.pricing') }}" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('marketing.pricing') ? 'text-indigo-600' : 'text-gray-900' }}">
                        Pricing
                    </a>
                    <a href="{{ route('marketing.faq') }}" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('marketing.faq') ? 'text-indigo-600' : 'text-gray-900' }}">
                        FAQ
                    </a>
                    <a href="{{ route('marketing.contact') }}" class="text-gray-900 hover:text-indigo-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('marketing.contact') ? 'text-indigo-600' : 'text-gray-900' }}">
                        Contact
                    </a>
                </nav>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Get Started
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon when menu is closed -->
                        <svg id="menu-closed-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Icon when menu is open -->
                        <svg id="menu-open-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div id="mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('marketing.home') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('marketing.home') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-900 hover:bg-gray-50' }}">
                    Home
                </a>
                <a href="{{ route('marketing.how-it-works') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('marketing.how-it-works') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-900 hover:bg-gray-50' }}">
                    How It Works
                </a>
                <a href="{{ route('marketing.pricing') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('marketing.pricing') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-900 hover:bg-gray-50' }}">
                    Pricing
                </a>
                <a href="{{ route('marketing.faq') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('marketing.faq') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-900 hover:bg-gray-50' }}">
                    FAQ
                </a>
                <a href="{{ route('marketing.contact') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('marketing.contact') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-900 hover:bg-gray-50' }}">
                    Contact
                </a>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-3">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="ml-4 block px-3 py-2 rounded-md text-base font-medium bg-indigo-600 text-white hover:bg-indigo-700">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <div class="space-y-8 xl:col-span-1">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-xl">OR</span>
                        </div>
                        <span class="text-xl font-bold text-white">Oregon Past Due Rent</span>
                    </div>
                    <p class="text-gray-300 text-base">
                        Making Oregon past due rent notices simple, affordable, and legally compliant.
                    </p>
                    <div class="flex space-x-6">
                        <!-- Social Media Links (if applicable) -->
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Services
                            </h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="{{ route('marketing.how-it-works') }}" class="text-base text-gray-300 hover:text-white">
                                        How It Works
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.pricing') }}" class="text-base text-gray-300 hover:text-white">
                                        Pricing
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.faq') }}" class="text-base text-gray-300 hover:text-white">
                                        FAQ
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Support
                            </h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="{{ route('marketing.contact') }}" class="text-base text-gray-300 hover:text-white">
                                        Contact
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.about') }}" class="text-base text-gray-300 hover:text-white">
                                        About Us
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.testimonials') }}" class="text-base text-gray-300 hover:text-white">
                                        Testimonials
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Legal
                            </h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="{{ route('marketing.privacy-policy') }}" class="text-base text-gray-300 hover:text-white">
                                        Privacy Policy
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('marketing.terms') }}" class="text-base text-gray-300 hover:text-white">
                                        Terms of Service
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                                Account
                            </h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="{{ route('login') }}" class="text-base text-gray-300 hover:text-white">
                                        Log In
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}" class="text-base text-gray-300 hover:text-white">
                                        Create Account
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-700 pt-8">
                <p class="text-base text-gray-400 xl:text-center">
                    &copy; {{ date('Y') }} Oregon Past Due Rent. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile menu toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuClosedIcon = document.getElementById('menu-closed-icon');
            const menuOpenIcon = document.getElementById('menu-open-icon');

            mobileMenuButton.addEventListener('click', function() {
                const isMenuOpen = mobileMenu.classList.toggle('hidden');

                if (isMenuOpen) {
                    menuClosedIcon.classList.remove('hidden');
                    menuOpenIcon.classList.add('hidden');
                } else {
                    menuClosedIcon.classList.add('hidden');
                    menuOpenIcon.classList.remove('hidden');
                }
            });
        });
    </script>
</body>

</html>