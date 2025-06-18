<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold text-gray-900">Realtors</h1>
                <p class="mt-2 text-sm text-gray-700">
                    A list of all realtors imported from CSV files. Total: {{ number_format($realtors->total()) }} realtors
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2">
                <button wire:click="export" type="button"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Search and Column Selector -->
        <div class="mt-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1 max-w-lg">
                    <label for="search" class="sr-only">Search realtors</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="search" id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Search by name, email, office, city, phone, license...">
                    </div>
                </div>
                
                <div class="relative">
                    <button wire:click="toggleColumnSelector" type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                        Columns
                    </button>
                    
                    <!-- Column Selector Dropdown -->
                    @if($showColumnSelector)
                    <div class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Select Columns</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach($availableColumns as $column => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        wire:click="toggleColumn('{{ $column }}')"
                                        @if(in_array($column, $selectedColumns)) checked @endif
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-6 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($selectedColumns as $column)
                            <th scope="col" 
                                @if(in_array($column, ['csv_id', 'full_name', 'email', 'office_name', 'city', 'state', 'county', 'phone', 'mobile', 'license_type', 'license_number', 'expiration_date', 'association', 'agency', 'listings', 'listings_volume', 'sold', 'sold_volume']))
                                wire:click="sortBy('{{ $column }}')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                @else
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                @endif
                                >
                                <div class="flex items-center">
                                    {{ $availableColumns[$column] }}
                                    @if(in_array($column, ['csv_id', 'full_name', 'email', 'office_name', 'city', 'state', 'county', 'phone', 'mobile', 'license_type', 'license_number', 'expiration_date', 'association', 'agency', 'listings', 'listings_volume', 'sold', 'sold_volume']))
                                        @if($sortField === $column)
                                            @if($sortDirection === 'asc')
                                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                            @else
                                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($realtors as $realtor)
                        <tr class="hover:bg-gray-50">
                            @foreach($selectedColumns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($column === 'email')
                                    <a href="mailto:{{ $realtor->$column }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $realtor->$column }}
                                    </a>
                                @elseif($column === 'phone' || $column === 'mobile')
                                    @if($realtor->$column)
                                        <a href="tel:{{ $realtor->$column }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $realtor->$column }}
                                        </a>
                                    @endif
                                @elseif($column === 'expiration_date')
                                    @if($realtor->$column)
                                        {{ $realtor->$column->format('M d, Y') }}
                                        @if($realtor->$column->isPast())
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Expired
                                            </span>
                                        @elseif($realtor->$column->diffInDays(now()) <= 90)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Expiring Soon
                                            </span>
                                        @endif
                                    @endif
                                @elseif($column === 'listings_volume' || $column === 'sold_volume')
                                    @if($realtor->$column)
                                        ${{ number_format($realtor->$column, 0) }}
                                    @endif
                                @elseif($column === 'email_status')
                                    @if($realtor->$column === 'ok')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            OK
                                        </span>
                                    @elseif($realtor->$column)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $realtor->$column }}
                                        </span>
                                    @endif
                                @else
                                    {{ $realtor->$column }}
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($selectedColumns) }}" class="px-6 py-12">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No realtors found</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if($search)
                                            Try adjusting your search criteria.
                                        @else
                                            Import realtor data using the CSV import command.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($realtors->hasPages())
        <div class="mt-6">
            {{ $realtors->links() }}
        </div>
        @endif
    </div>

    <!-- Click outside to close column selector -->
    @if($showColumnSelector)
    <div wire:click="toggleColumnSelector" class="fixed inset-0 z-0"></div>
    @endif
</div>