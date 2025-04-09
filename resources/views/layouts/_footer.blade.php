{{-- Footer for the application layout --}}
<footer class="mt-auto py-6 bg-white border-t border-gray-200">
    <div class="container mx-auto text-center text-sm text-gray-600">
        Rental Notice is a product of <a href="https://cloudmanic.com/" target="_blank" rel="noopener"
            class="text-indigo-600 hover:text-indigo-500">Cloudmanic Labs, LLC</a>. Copyright © {{ date('Y') }}.
        All rights reserved.
        <span class="mx-2">•</span>
        <a href="{{ route('marketing.privacy-policy') }}" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
        <span class="mx-2">•</span>
        <a href="{{ route('marketing.terms') }}" class="text-indigo-600 hover:text-indigo-500">Terms</a>
        <span class="mx-2">•</span>
        <a href="{{ route('marketing.refund-policy') }}" class="text-indigo-600 hover:text-indigo-500">Refund Policy</a>
    </div>
</footer>