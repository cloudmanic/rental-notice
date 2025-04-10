<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Profile Settings</h1>
                <p class="mt-2 text-sm text-gray-700">Manage your account settings and change your password.</p>
            </div>
        </div>

        <div class="mt-8 space-y-10">
            <!-- Profile Information Section -->
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Update your account's profile information.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                @if (session('message'))
                                <div class="mb-4 rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <form wire:submit="updateProfile" class="space-y-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <!-- First Name -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="first_name" class="block text-sm font-medium text-gray-700">First name <span class="text-red-600">*</span></label>
                                            <input wire:model="first_name" type="text" id="first_name" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @error('first_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last name <span class="text-red-600">*</span></label>
                                            <input wire:model="last_name" type="text" id="last_name" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @error('last_name') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="col-span-6">
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-600">*</span></label>
                                            <input wire:model="email" type="email" id="email" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @error('email') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                                            <span wire:loading.remove wire:target="updateProfile">Save</span>
                                            <span wire:loading wire:target="updateProfile">Saving...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Password Section -->
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Update Password</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Ensure your account is using a long, random password to stay secure.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                @if (session('password_message'))
                                <div class="mb-4 rounded-md bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-800">{{ session('password_message') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <form wire:submit="updatePassword" class="space-y-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <!-- Current Password -->
                                        <div class="col-span-6">
                                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password <span class="text-red-600">*</span></label>
                                            <input wire:model="current_password" type="password" id="current_password" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @error('current_password') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- New Password -->
                                        <div class="col-span-6">
                                            <label for="password" class="block text-sm font-medium text-gray-700">New Password <span class="text-red-600">*</span></label>
                                            <input wire:model="password" type="password" id="password" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            @error('password') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-span-6">
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password <span class="text-red-600">*</span></label>
                                            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="mt-1 block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                                            <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                            <span wire:loading wire:target="updatePassword">Updating...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>