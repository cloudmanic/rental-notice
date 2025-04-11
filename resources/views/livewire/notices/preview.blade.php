<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-900">Notice Preview</h1>

        <!-- Action Buttons - Moved to top -->
        <div class="flex space-x-4">
            <button wire:click="backToEdit"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Back to Edit
            </button>

            <button wire:click="keepAsDraft"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Keep as Draft
            </button>

            <button wire:click="proceedToPayment"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Proceed to Payment
            </button>
        </div>
    </div>

    <!-- Information Alert -->
    <div class="mb-6 p-4 bg-blue-50 text-blue-800 rounded-md">
        <p class="text-center">This is the notice that's going to get served to your tenant. Please review before
            proceeding.</p>
    </div>

    <div class="flex flex-col space-y-6">
        <!-- PDF Viewer -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="border border-gray-200 rounded-md h-[800px]">
                <!-- Embed PDF here - this is a placeholder -->
                <embed src="{{ route('notices.pdf', $notice->id) }}" type="application/pdf" width="100%" height="100%"
                    class="border-0" />
                <!-- Note: You'll need to create a route and controller method to generate/serve the PDF -->
            </div>
        </div>

        <!-- Notice Information Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Notice Summary</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Notice Type</p>
                        <p class="text-base text-gray-900">{{ $notice->noticeType->name }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Tenant</p>
                        @foreach($notice->tenants as $tenant)
                        <p class="text-base text-gray-900">{{ $tenant->full_name }}</p>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Total Amount Due</p>
                        <p class="text-lg font-bold text-gray-900">${{ number_format($notice->totalCharges, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Service Fee</p>
                        <p class="text-base text-gray-900">${{ number_format($notice->price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Duplicate Action Buttons at the bottom for convenience -->
        <div class="flex justify-between">
            <div class="flex space-x-4">
                <button wire:click="backToEdit"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Back to Edit
                </button>

                <button wire:click="keepAsDraft"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Keep as Draft
                </button>
            </div>

            <button wire:click="proceedToPayment"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Proceed to Payment
            </button>
        </div>
    </div>
</div>