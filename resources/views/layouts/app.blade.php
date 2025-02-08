<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rental Notice') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased h-full">
    <div class="min-h-full bg-gray-100 flex flex-col">
        @include('layouts._navigation')
        <div class="flex-1 bg-gray-50">
            <div class="py-10">
                <main>
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="px-4 py-8 sm:px-0">
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="px-4 py-5 sm:p-6">
                                    @yield('content')
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        @include('layouts._footer')
    </div>
</body>

</html>