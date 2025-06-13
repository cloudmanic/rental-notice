@props([
    'title',
    'subtitle' => null,
    'description',
    'primaryButtonText' => 'Get Started Today',
    'primaryButtonUrl' => null,
    'secondaryButtonText' => 'Learn How It Works',
    'secondaryButtonUrl' => null,
    'showSpecialOffer' => false,
    'specialOfferText' => null
])

<div class="relative bg-indigo-800 overflow-hidden">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover"
            src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1973&q=80"
            alt="Oregon apartment building">
        <div class="absolute inset-0 bg-indigo-800 opacity-75"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
        @if($showSpecialOffer && $specialOfferText)
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mb-6 inline-block">
                <p class="text-white text-lg font-medium">
                    {!! $specialOfferText !!}
                </p>
            </div>
        @endif
        
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            {!! $title !!}
        </h1>
        
        <p class="mt-6 text-xl text-indigo-100 max-w-3xl">
            {!! $description !!}
        </p>
        
        <div class="mt-10 flex flex-col sm:flex-row gap-4 sm:gap-6">
            <a href="{{ $primaryButtonUrl ?? route('register') }}"
                class="inline-flex w-full sm:w-auto items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:text-lg">
                {!! $primaryButtonText !!}
            </a>
            <a href="{{ $secondaryButtonUrl ?? route('marketing.how-it-works') }}"
                class="inline-flex w-full sm:w-auto items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:text-lg">
                {{ $secondaryButtonText }}
            </a>
        </div>
    </div>
</div>