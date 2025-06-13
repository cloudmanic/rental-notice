@props([
    'rating' => 5,
    'testimonial',
    'name',
    'title'
])

<div class="bg-gray-50 rounded-lg p-8">
    <div class="flex items-center mb-4">
        <x-marketing.star-rating :rating="$rating" />
    </div>
    <p class="text-gray-600 mb-4">
        "{{ $testimonial }}"
    </p>
    <p class="font-semibold text-gray-900">{{ $name }}</p>
    <p class="text-sm text-gray-500">{{ $title }}</p>
</div>