<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Add Tenant</h1>
            <p class="mt-2 text-sm text-gray-700">Create a new tenant record in your account.</p>
        </div>
    </div>

    <form wire:submit="save" class="mt-6 space-y-8">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <!-- Name Section -->
            <div class="sm:col-span-3">
                <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                <div class="mt-2">
                    <input wire:model.live="first_name" type="text" id="first_name"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('first_name') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('first_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-3">
                <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">Last name</label>
                <div class="mt-2">
                    <input wire:model.live="last_name" type="text" id="last_name"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('last_name') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('last_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Contact Information -->
            <div class="sm:col-span-3">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                <div class="mt-2">
                    <input wire:model.live="email" type="email" id="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-3">
                <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone number</label>
                <div class="mt-2">
                    <input wire:model.live="phone" type="tel" id="phone" placeholder="(555) 555-5555"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('phone') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('phone') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <!-- Address Section -->
            <div class="col-span-full">
                <label for="address_1" class="block text-sm font-medium leading-6 text-gray-900">Street address</label>
                <div class="mt-2">
                    <input wire:model.live="address_1" type="text" id="address_1"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('address_1') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
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
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('address_2') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('address_2') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2 sm:col-start-1">
                <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                <div class="mt-2">
                    <input wire:model.live="city" type="text" id="city"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('city') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('city') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="state" class="block text-sm font-medium leading-6 text-gray-900">State</label>
                <div class="mt-2">
                    <input wire:model.live="state" type="text" id="state" maxlength="2" placeholder="CA"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('state') ring-red-300 text-red-900 focus:ring-red-500 @enderror uppercase">
                </div>
                @error('state') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="zip" class="block text-sm font-medium leading-6 text-gray-900">ZIP code</label>
                <div class="mt-2">
                    <input wire:model.live="zip" type="text" id="zip" placeholder="12345"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('zip') ring-red-300 text-red-900 focus:ring-red-500 @enderror">
                </div>
                @error('zip') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a wire:navigate href="{{ route('tenants.index') }}"
                class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancel
            </a>
            <button type="submit"
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Save</span>
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
                        Saving...
                    </div>
                </span>
            </button>
        </div>
    </form>
</div>