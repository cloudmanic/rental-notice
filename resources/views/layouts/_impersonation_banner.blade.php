@if(session('impersonating'))
<div class="bg-amber-500 text-white py-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium">
                    You are currently impersonating {{ auth()->user()->full_name }} ({{ auth()->user()->account->name }})
                </p>
            </div>
            <a href="{{ route('leave-impersonation') }}" class="text-white font-medium text-sm underline hover:text-amber-100">
                Return to your account
            </a>
        </div>
    </div>
</div>
@endif