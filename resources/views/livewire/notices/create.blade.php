<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create Late Rent Notice</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="createNotice">
            <!-- Notice Type Selection -->
            <div class="mb-6">
                <label for="notice_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Notice Type
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Select the type of late rent notice required for your state. Each notice type is court-tested to ensure compliance with local regulations.
                        </div>
                    </span>
                </label>
                <select id="notice_type_id" wire:model="notice.notice_type_id" class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Select a notice type</option>
                    @foreach($noticeTypes as $noticeType)
                    <option value="{{ $noticeType->id }}">{{ $noticeType->name }} - ${{ number_format($noticeType->price, 2) }}</option>
                    @endforeach
                </select>
                @error('notice.notice_type_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Agent Selection -->
            <div class="mb-6">
                <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Agent (Landlord/Property Manager)
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Select the landlord or property manager who will be sending this notice. This is the person who will appear as the sender.
                        </div>
                    </span>
                </label>
                <div class="flex items-center space-x-2">
                    <select id="agent_id" wire:model="notice.agent_id" class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">Select an agent</option>
                        @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="openAgentModal" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New
                    </button>
                </div>
                @error('notice.agent_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Tenant Selection with Search -->
            <div class="mb-6">
                <label for="tenant_search" class="block text-sm font-medium text-gray-700 mb-1">
                    Tenant
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Search for an existing tenant or create a new one. The tenant will receive the late rent notice.
                        </div>
                    </span>
                </label>
                <div class="flex items-center space-x-2 relative">
                    <div class="relative w-full">
                        <input type="text" id="tenant_search" wire:model.live="searchTenant" placeholder="Search for tenant by name or email..." class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <input type="hidden" wire:model="notice.tenant_id">

                        @if($tenants->count() > 0 && strlen($searchTenant) >= 2 && !$selectedTenantId)
                        <div class="absolute z-10 mt-2 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-y-auto">
                            @foreach($tenants as $tenant)
                            <div wire:click="selectTenant({{ $tenant->id }})" class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <div class="font-medium">{{ $tenant->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $tenant->email }}</div>
                                <div class="text-xs text-gray-500">{{ $tenant->full_address }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <button type="button" wire:click="openTenantModal" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New
                    </button>
                </div>
                @error('notice.tenant_id') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Past Due Rent -->
            <div class="mb-6">
                <label for="past_due_rent" class="block text-sm font-medium text-gray-700 mb-1">
                    Past Due Rent Amount
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Enter the total amount of unpaid rent. This will be displayed prominently in the notice.
                        </div>
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" id="past_due_rent" wire:model="notice.past_due_rent" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('notice.past_due_rent') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Late Charges -->
            <div class="mb-6">
                <label for="late_charges" class="block text-sm font-medium text-gray-700 mb-1">
                    Late Charges
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Enter any late fees or penalties associated with the late rent payment.
                        </div>
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" step="0.01" id="late_charges" wire:model="notice.late_charges" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('notice.late_charges') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Other Charges (5 sets) -->
            @for ($i = 1; $i <= 5; $i++)
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="other_{{ $i }}_title" class="block text-sm font-medium text-gray-700 mb-1">
                        Other Charge #{{ $i }} Title
                        <span class="ml-1 relative group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                                Optional: Enter a description for any additional charges (e.g., "Utility Payment", "Property Damage").
                            </div>
                        </span>
                    </label>
                    <input type="text" id="other_{{ $i }}_title" wire:model="notice.other_{{ $i }}_title" class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="(Optional) e.g., Utility Payment">
                    @error("notice.other_{{ $i }}_title") <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="other_{{ $i }}_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Amount
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" step="0.01" id="other_{{ $i }}_price" wire:model="notice.other_{{ $i }}_price" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error("notice.other_{{ $i }}_price") <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
    </div>
    @endfor

    <!-- Additional Options -->
    <div class="mb-6 mt-8 space-y-4">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="payment_other_means" wire:model="notice.payment_other_means" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="payment_other_means" class="font-medium text-gray-700">
                    Payment by Other Means
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Check this if you allow payment by methods other than those specified in the lease or rental agreement.
                        </div>
                    </span>
                </label>
            </div>
        </div>

        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="include_all_other_occupents" wire:model="notice.include_all_other_occupents" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="include_all_other_occupents" class="font-medium text-gray-700">
                    Include All Other Occupants
                    <span class="ml-1 relative group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="absolute z-10 hidden group-hover:block bg-gray-800 text-white text-xs rounded p-2 w-64 bottom-full left-0">
                            Check this to include all other occupants in the property on the notice, not just the primary tenant.
                        </div>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-x-6">
        <a wire:navigate href="{{ route('notices.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700">
            Cancel
        </a>
        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- This is a placeholder that would have the tenant creation form -->
            <div class="text-gray-500 mb-4">
                <p>The tenant creation form would go here, similar to your existing tenant creation form.</p>
                <p class="mt-2">For implementation, you would either include the tenant form directly or load a Livewire component here.</p>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeTenantModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Tenant
                </button>
            </div>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- This is a placeholder that would have the agent creation form -->
            <div class="text-gray-500 mb-4">
                <p>The agent creation form would go here, similar to your existing agent creation form.</p>
                <p class="mt-2">For implementation, you would either include the agent form directly or load a Livewire component here.</p>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeAgentModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Agent
                </button>
            </div>
        </div>
    </div>
</div>
@endif
</div>