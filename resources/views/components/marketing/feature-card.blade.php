@props([
    'icon',
    'title',
    'description'
])

<div class="relative">
    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-600 text-white">
        {!! $icon !!}
    </div>
    <div class="mt-4">
        <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        <p class="mt-2 text-base text-gray-500">
            {{ $description }}
        </p>
    </div>
</div>