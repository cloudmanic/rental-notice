<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <a href="{{ route('notices.index') }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Notice #{{ $notice->id }}</h1>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="updateNotice">
            <!-- Notice Type Selection -->
            <div class="mb-6">
                <label for="notice_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Notice Type
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Select the type of late rent notice required for your state. Each notice type is
                            court-tested to ensure compliance with local regulations.
                        </div>
                    </span>
                </label>
                <select id="notice_type_id" wire:model="notice.notice_type_id"
                    class="block w-full h-[38px] rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Select a notice type</option>
                    @foreach($noticeTypes as $noticeType)
                    <option value="{{ $noticeType->id }}">{{ $noticeType->name }} -
                        ${{ number_format($noticeType->price, 2) }}</option>
                    @endforeach
                </select>
                @error('notice.notice_type_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Agent Selection -->
            <div class="mb-6">
                <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Agent (Landlord/Property Manager)
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Select the landlord or property manager who will be sending this notice. This is the person
                            who will appear as the sender.
                        </div>
                    </span>
                </label>
                <div class="flex items-center space-x-2">
                    <select id="agent_id" wire:model.live="notice.agent_id"
                        class="block w-full h-[38px] rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Select an agent</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('notice.agent_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Tenant Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tenants
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Select one or more tenants who will receive the notice.
                        </div>
                    </span>
                </label>

                <!-- Selected Tenants List -->
                <div class="mb-3">
                    @if(count($selectedTenants) > 0)
                    <div class="bg-gray-50 rounded-md p-3 mb-3">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Selected Tenants:</h3>
                        <ul class="space-y-2">
                            @foreach($selectedTenants as $tenant)
                            <li class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <label class="text-sm text-gray-700">
                                        {{ $tenant['name'] }}
                                    </label>
                                </div>
                                <button type="button" wire:click="removeTenant({{ $tenant['id'] }})"
                                    class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @error('selectedTenants') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Tenant Search -->
                <div class="flex items-center space-x-2 relative">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="tenant_search" wire:model.live="searchTenant"
                            placeholder="Search for tenant by name or email..."
                            class="block w-full rounded-md border-0 py-1.5 pl-10 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                        @if($tenants->count() > 0 && strlen($searchTenant) >= 2)
                        <div
                            class="absolute z-10 mt-2 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-y-auto">
                            @foreach($tenants as $tenant)
                            <div wire:click="addTenant({{ $tenant->id }})"
                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <div class="font-medium">{{ $tenant->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $tenant->email }}</div>
                                <div class="text-xs text-gray-500">{{ $tenant->full_address }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Past Due Rent -->
            <div class="mb-6">
                <label for="past_due_rent" class="block text-sm font-medium text-gray-700 mb-1">
                    Past Due Rent Amount
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Enter the total amount of unpaid rent. This will be displayed prominently in the notice.
                        </div>
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" id="past_due_rent" wire:model="notice.past_due_rent"
                        class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('notice.past_due_rent') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Late Charges -->
            <div class="mb-6">
                <label for="late_charges" class="block text-sm font-medium text-gray-700 mb-1">
                    Late Charges
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Enter any late fees or penalties associated with the late rent payment.
                        </div>
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" id="late_charges" wire:model="notice.late_charges"
                        class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('notice.late_charges') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Other Charges (Dynamic) -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Other Charges
                        <span class="ml-1 relative group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div
                                class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                                Optional: Add additional charges such as utilities, property damage, or other fees.
                            </div>
                        </span>
                    </label>
                    @if($visibleCharges < 5) <button type="button" wire:click="addCharge"
                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Charge
                        </button>
                        @endif
                </div>

                <!-- Dynamically show charge inputs based on visibleCharges property -->
                @for ($i = 1; $i <= $visibleCharges; $i++) <div
                    class="mb-3 grid grid-cols-1 md:grid-cols-10 gap-4 bg-gray-50 p-3 rounded-md relative">
                    <div class="md:col-span-7">
                        <label for="other_{{ $i }}_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Charge #{{ $i }} Title
                        </label>
                        <input type="text" id="other_{{ $i }}_title" wire:model="notice.other_{{ $i }}_title"
                            class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="e.g., Utility Payment">
                        @error("notice.other_{{ $i }}_title") <div class="mt-1 text-sm text-red-600">{{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="other_{{ $i }}_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Amount
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" id="other_{{ $i }}_price"
                                wire:model="notice.other_{{ $i }}_price"
                                class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error("notice.other_{{ $i }}_price") <div class="mt-1 text-sm text-red-600">{{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="md:col-span-1 flex items-end justify-center pb-1">
                        <button type="button" wire:click="removeCharge({{ $i }})"
                            class="text-red-500 hover:text-red-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
            </div>
            @endfor

            @if($visibleCharges === 0)
            <div class="text-center py-4 bg-gray-50 rounded-md">
                <span class="text-gray-500">Click "Add Charge" to include additional charges</span>
            </div>
            @endif
    </div>

    <!-- Additional Options -->
    <div class="mb-6 mt-8 space-y-4">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="payment_other_means" wire:model="notice.payment_other_means" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="payment_other_means" class="font-medium text-gray-700">
                    Payment by Other Means
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Check this if you allow payment by methods other than those specified in the lease or rental
                            agreement.
                        </div>
                    </span>
                </label>
            </div>
        </div>

        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="include_all_other_occupents" wire:model="notice.include_all_other_occupents" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="include_all_other_occupents" class="font-medium text-gray-700">
                    Include All Other Occupants
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div
                            class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Check this to include all other occupants in the property on the notice.
                        </div>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-x-6">
        <a href="{{ route('notices.show', $notice->id) }}"
            class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">
            Cancel
        </a>
        <button type="submit"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Save Changes
        </button>
    </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.classList.add('opacity-0');
                setTimeout(() => {
                    flashMessage.remove();
                }, 500);
            }, 5000);
        }
    });
</script>
</div>