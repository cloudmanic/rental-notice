<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="text-center py-12">
                    <h2 class="text-2xl font-bold mb-6">Generating Your Notice</h2>

                    <div class="flex justify-center mb-6">
                        <!-- SVG Spinner -->
                        <svg class="animate-spin h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <p class="text-lg mb-4">Please wait while we generate your notice PDF.</p>
                    <p class="text-sm text-gray-500 mb-8">This may take a few moments. You'll be automatically
                        redirected when the document is ready.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Poll for updates -->
    <div wire:poll.{{ $pollingInterval }}ms="checkPdfStatus"></div>
</div>