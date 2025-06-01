<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7GFRHNP5ML"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-7GFRHNP5ML');
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Oregon Past Due Rent')</title>

    <script defer data-domain="oregonpastduerent.com"
        src="https://plausible.io/js/script.pageview-props.revenue.tagged-events.js"></script>
    <script>
    window.plausible = window.plausible || function() {
        (window.plausible.q = window.plausible.q || []).push(arguments)
    }
    </script>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased h-full">
    <div class="min-h-full bg-gray-50 flex flex-col">
        <main class="flex-1">
            @yield('content')
        </main>
        @include('layouts._footer')
    </div>
</body>

</html>