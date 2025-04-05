<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Agent</h1>
            <p class="mt-2 text-sm text-gray-700">Update agent information in your account.</p>
        </div>
    </div>

    <form wire:submit="update" class="mt-6 space-y-8">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <!-- Name Section -->
            <div class="sm:col-span-4">
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full name <span
                        class="text-red-600">*</span></label>
                <div class="mt-2">
                    <input wire:model.live="name" type="text" id="name"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('name') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Contact Information -->
            <div class="sm:col-span-3">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                <div class="mt-2">
                    <input wire:model.live="email" type="email" id="email"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-3">
                <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone number</label>
                <div class="mt-2">
                    <input wire:model.live="phone" type="tel" id="phone" placeholder="555-555-5555"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('phone') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('phone') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Address Section -->
            <div class="col-span-full">
                <label for="address_1" class="block text-sm font-medium leading-6 text-gray-900">Street address <span
                        class="text-red-600">*</span></label>
                <div class="mt-2">
                    <input wire:model.live="address_1" type="text" id="address_1"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('address_1') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('address_1') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="col-span-full">
                <label for="address_2" class="block text-sm font-medium leading-6 text-gray-900">
                    Apartment, suite, etc.
                    <span class="text-gray-500">(optional)</span>
                </label>
                <div class="mt-2">
                    <input wire:model.live="address_2" type="text" id="address_2"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('address_2') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('address_2') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2 sm:col-start-1">
                <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City <span
                        class="text-red-600">*</span></label>
                <div class="mt-2">
                    <input wire:model.live="city" type="text" id="city"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('city') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('city') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <div>
                    <label for="state" class="block text-sm font-medium leading-6 text-gray-900">State <span
                            class="text-red-600">*</span></label>
                    <div class="mt-2">
                        <select wire:model.live="state" id="state"
                            class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">Select a state</option>
                            @foreach ($states as $abbr => $name)
                            <option value="{{ $abbr }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @error('state') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="zip" class="block text-sm font-medium leading-6 text-gray-900">ZIP code <span
                        class="text-red-600">*</span></label>
                <div class="mt-2">
                    <input wire:model.live="zip" type="text" id="zip" placeholder="12345"
                        class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('zip') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('zip') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex items-center gap-x-6">
            <a wire:navigate href="{{ route('agents.index') }}"
                class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="button" wire:click="confirmDelete"
                class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                Delete
            </button>
            <button type="submit"
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Update</span>
                <span wire:loading>
                    <div class="flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Updating...
                    </div>
                </span>
            </button>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Delete Agent</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Are you sure you want to delete this agent? This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="delete"
                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Delete</button>
                        <button type="button" wire:click="cancelDelete"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>