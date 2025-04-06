<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create Late Rent Notice</h1>
    </div>

    <!-- Flash Messages -->
    @if($showMessage)
    <div id="flash-message"
        class="mb-6 p-4 {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-md">
        {{ $message }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="createNotice">
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
                        class="block w-full h-[38px] rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        @if(count($agents)===1) wire:init="$set('notice.agent_id', {{ $agents->first()->id }})" @endif>
                        <option value="">Select an agent</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="openAgentModal"
                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New
                    </button>
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
                    <button type="button" wire:click="openTenantModal"
                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New
                    </button>
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
        <a wire:navigate href="{{ route('notices.index') }}"
            class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">
            Cancel
        </a>
        <button type="submit"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Create Notice
        </button>
    </div>
    </form>
</div>

<!-- Tenant Creation Modal -->
@if($showTenantModal)
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Create New Tenant</h2>
                <button wire:click="closeTenantModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="createTenant" class="space-y-6">
                <!-- Name Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tenant-first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="tenant-first-name" wire:model="tenant.first_name"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('tenant.first_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="tenant-last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="tenant-last-name" wire:model="tenant.last_name"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('tenant.last_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tenant-email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="tenant-email" wire:model="tenant.email"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('tenant.email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="tenant-phone" class="block text-sm font-medium text-gray-700">Phone (XXX-XXX-XXXX)</label>
                        <input type="text" id="tenant-phone" wire:model="tenant.phone"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('tenant.phone') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="tenant-address-1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input type="text" id="tenant-address-1" wire:model="tenant.address_1"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('tenant.address_1') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="tenant-address-2" class="block text-sm font-medium text-gray-700">Address Line 2 (Optional)</label>
                    <input type="text" id="tenant-address-2" wire:model="tenant.address_2"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('tenant.address_2') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="tenant-city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" id="tenant-city" wire:model="tenant.city"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('tenant.city') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- State and ZIP in a grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- State -->
                    <div>
                        <label for="tenant-state" class="block text-sm font-medium text-gray-700">State</label>
                        <select id="tenant-state" wire:model="tenant.state"
                            class="mt-1 block w-full h-[38px] rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach($states as $abbr => $name)
                            <option value="{{ $abbr }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('tenant.state') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <!-- ZIP -->
                    <div>
                        <label for="tenant-zip" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                        <input type="text" id="tenant-zip" wire:model="tenant.zip" placeholder="12345 or 12345-6789"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('tenant.zip') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="closeTenantModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Agent Creation Modal -->
@if($showAgentModal)
<div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Create New Agent</h2>
                <button wire:click="closeAgentModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="createAgent" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="agent-name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="agent-name" wire:model="agent.name"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="agent-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="agent-email" wire:model="agent.email"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="agent-phone" class="block text-sm font-medium text-gray-700">Phone
                        (XXX-XXX-XXXX)</label>
                    <input type="text" id="agent-phone" wire:model="agent.phone"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.phone') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="agent-address-1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input type="text" id="agent-address-1" wire:model="agent.address_1"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.address_1') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="agent-address-2" class="block text-sm font-medium text-gray-700">Address Line 2
                        (Optional)</label>
                    <input type="text" id="agent-address-2" wire:model="agent.address_2"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.address_2') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="agent-city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" id="agent-city" wire:model="agent.city"
                        class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    @error('agent.city') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <!-- State and ZIP in a grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- State -->
                    <div>
                        <label for="agent-state" class="block text-sm font-medium text-gray-700">State</label>
                        <select id="agent-state" wire:model="agent.state"
                            class="mt-1 block w-full h-[38px] rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach($states as $abbr => $name)
                            <option value="{{ $abbr }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('agent.state') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <!-- ZIP -->
                    <div>
                        <label for="agent-zip" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                        <input type="text" id="agent-zip" wire:model="agent.zip" placeholder="12345 or 12345-6789"
                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @error('agent.zip') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="closeAgentModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Agent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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