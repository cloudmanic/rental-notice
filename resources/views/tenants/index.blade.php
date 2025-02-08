@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    @include('layouts._navigation')

    <div class="py-10">
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 py-8 sm:px-0">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Tenants</h1>
                            <!-- Content will go here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection