@props([
    'step',
    'title',
    'description'
])

<div class="relative text-center">
    <div class="flex items-center justify-center mx-auto h-16 w-16 rounded-full bg-indigo-600 text-white">
        <span class="text-2xl font-bold">{{ $step }}</span>
    </div>
    <h3 class="mt-6 text-xl font-medium text-gray-900">{{ $title }}</h3>
    <p class="mt-2 text-base text-gray-500">
        {{ $description }}
    </p>
</div>