<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Oregon Past Due Rent')</title>
    <link rel="canonical" href="{{ url()->current() }}" />

    <script defer data-domain="oregonpastduerent.com"
        src="https://plausible.io/js/script.pageview-props.revenue.tagged-events.js"></script>
    <script>
    window.plausible = window.plausible || function() {
        (window.plausible.q = window.plausible.q || []).push(arguments)
    }
    </script>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased min-h-full flex flex-col">
    <!-- Impersonation Banner -->
    @include('layouts._impersonation_banner')

    <div class="flex-1 flex flex-col bg-gray-100">
        <div class="flex-1 flex flex-col bg-gray-50">
            @include('layouts._navigation')

            <div class="flex-1 py-6">
                <main>
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="px-4 sm:px-0">
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    {{ $slot }}
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        @include('layouts._footer')
    </div>
    @livewireScripts
</body>

</html>