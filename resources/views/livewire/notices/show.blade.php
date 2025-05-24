<div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <a href="{{ route('notices.index') }}" class="text-indigo-600 hover:text-indigo-900 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900">Notice #{{ $notice->id }}</h1>
        </div>

        <div class="flex items-center space-x-3">
            @if($notice->status === 'draft')
            <a href="{{ route('notices.edit', $notice->id) }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            @endif

            <!-- Super Admin Certificate PDF Upload Button -->
            @if((auth()->user()->isSuperAdmin() || session('impersonating')))
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload Signed Certificate
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg p-4 z-10 w-72">
                    <form wire:submit.prevent="uploadCertificatePdf">
                        <div class="mb-3">
                            <label for="certificatePdf" class="block text-sm font-medium text-gray-700 mb-1">
                                Certificate PDF
                            </label>
                            <input type="file" id="certificatePdf" wire:model="certificatePdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('certificatePdf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Upload
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Notice View/Download Dropdown Button -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Notice
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 bg-white shadow-lg rounded-lg p-3 z-10 w-80">
                    <a href="{{ route('notices.pdf', $notice->id) }}" target="_blank"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Notice
                    </a>
                    <a href="{{ route('notices.pdf', $notice->id) }}?download=true"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Notice
                    </a>

                    @if($notice->certificate_pdf && ($notice->status === 'served' || ($notice->status ===
                    'service_pending' && (auth()->user()->isSuperAdmin() || session('impersonating')))))
                    <hr class="my-2">
                    <a href="{{ route('notices.certificate-pdf', $notice->id) }}" target="_blank"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View Certificate
                    </a>
                    <a href="{{ route('notices.certificate-pdf', $notice->id) }}?download=true"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Certificate
                    </a>
                    @endif

                    @if((auth()->user()->isSuperAdmin() || session('impersonating')))
                    <hr class="my-2">
                    <a href="{{ route('notices.tenant-address-sheets', $notice->id) }}" target="_blank"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                        </svg>
                        View Tenant Address Sheets
                    </a>
                    <a href="{{ route('notices.tenant-address-sheets', $notice->id) }}?download=true"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Tenant Address Sheets
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('notices.agent-address-sheet', $notice->id) }}" target="_blank"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        View Agent Address Sheet
                    </a>
                    <a href="{{ route('notices.agent-address-sheet', $notice->id) }}?download=true"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Agent Address Sheet
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('notices.agent-cover-letter', $notice->id) }}" target="_blank"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View Agent Cover Letter
                    </a>
                    <a href="{{ route('notices.agent-cover-letter', $notice->id) }}?download=true"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Agent Cover Letter
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('notices.complete-print-package', $notice->id) }}"
                        class="block px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Download Complete Print Package
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
            {{ $notice->status === 'pending_payment' ? 'bg-yellow-100 text-yellow-800' : 
            ($notice->status === 'service_pending' ? 'bg-purple-100 text-purple-800' : 
            ($notice->status === 'served' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
            {{ ucfirst(str_replace('_', ' ', $notice->status)) }}
        </span>
    </div>

    <!-- Notice Content -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Notice Header Section -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">{{ $notice->noticeType->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Created on {{ $notice->created_at->format('M d, Y') }}</p>
        </div>

        <!-- Notice Details Section -->
        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <h3 class="text-md font-medium text-gray-900 mb-3">Notice Information</h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Notice Type</p>
                        <p class="text-sm text-gray-900">{{ $notice->noticeType->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Notice Price</p>
                        <p class="text-sm text-gray-900">${{ number_format($notice->price, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Past Due Rent</p>
                        <p class="text-sm text-gray-900">${{ number_format($notice->past_due_rent, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Late Charges</p>
                        <p class="text-sm text-gray-900">${{ number_format($notice->late_charges, 2) }}</p>
                    </div>

                    <!-- Other Charges -->
                    @php
                    $hasOtherCharges = false;
                    for ($i = 1; $i <= 5; $i++) { $title="other_{$i}_title" ; $price="other_{$i}_price" ; if
                        (!empty($notice->$title)) {
                        $hasOtherCharges = true;
                        break;
                        }
                        }
                        @endphp

                        @if($hasOtherCharges)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Other Charges</p>
                            <div class="space-y-1 mt-1">
                                @for ($i = 1; $i <= 5; $i++) @php $title="other_{$i}_title" ; $price="other_{$i}_price"
                                    ; @endphp @if(!empty($notice->$title))
                                    <div class="flex justify-between">
                                        <p class="text-sm text-gray-600">{{ $notice->$title }}</p>
                                        <p class="text-sm text-gray-900">${{ number_format($notice->$price, 2) }}</p>
                                    </div>
                                    @endif
                                    @endfor
                            </div>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Amount Due</p>
                            <p class="text-base font-bold text-gray-900">${{ number_format($notice->totalCharges, 2) }}
                            </p>
                        </div>

                        <!-- Additional Options -->
                        <div class="pt-3">
                            <p class="text-sm font-medium text-gray-500">Additional Options</p>
                            <ul class="mt-1 space-y-1">
                                <li class="text-sm text-gray-600 flex items-center">
                                    <span class="mr-2">
                                        @if($notice->payment_other_means)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        @endif
                                    </span>
                                    Payment by Other Means
                                </li>
                            </ul>
                        </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Tenant Information -->
                <h3 class="text-md font-medium text-gray-900 mb-3">Tenants</h3>
                <div class="space-y-4">
                    @foreach($notice->tenants as $tenant)
                    <div class="bg-gray-50 p-3 rounded-md">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-medium text-gray-900">{{ $tenant->full_name }}</h4>
                        </div>
                        <div class="text-xs text-gray-500 space-y-1">
                            @if($tenant->email)
                            <p>{{ $tenant->email }}</p>
                            @endif
                            @if($tenant->phone)
                            <p>{{ $tenant->phone }}</p>
                            @endif
                            <p>{{ $tenant->full_address }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Agent Information -->
                <h3 class="text-md font-medium text-gray-900 mb-3 mt-6">Agent Information</h3>
                <div class="bg-gray-50 p-3 rounded-md">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-sm font-medium text-gray-900">{{ $notice->agent->name }}</h4>
                    </div>
                    <div class="text-xs text-gray-500 space-y-1">
                        @if($notice->agent->email)
                        <p>{{ $notice->agent->email }}</p>
                        @endif
                        @if($notice->agent->phone)
                        <p>{{ $notice->agent->phone }}</p>
                        @endif
                        <p>{{ $notice->agent->address_1 }}</p>
                        @if($notice->agent->address_2)
                        <p>{{ $notice->agent->address_2 }}</p>
                        @endif
                        <p>{{ $notice->agent->city }}, {{ $notice->agent->state }} {{ $notice->agent->zip }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>