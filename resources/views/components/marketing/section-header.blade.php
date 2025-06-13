@props([
    'subtitle',
    'title',
    'description' => null,
    'centered' => true
])

<div class="{{ $centered ? 'text-center' : '' }}">
    <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">{{ $subtitle }}</h2>
    <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
        {{ $title }}
    </p>
    @if($description)
        <p class="mt-4 max-w-2xl {{ $centered ? 'mx-auto' : '' }} text-xl text-gray-500">
            {{ $description }}
        </p>
    @endif
</div>