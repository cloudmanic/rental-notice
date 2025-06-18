<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-xl font-bold text-gray-900">Past Due Rent</h1>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                <a wire:navigate href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Dashboard
                </a>
                <a wire:navigate href="{{ route('notices.index') }}"
                    class="{{ request()->routeIs('notices.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Notices
                </a>
                <a wire:navigate href="{{ route('tenants.index') }}"
                    class="{{ request()->routeIs('tenants.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                    Tenants
                </a>
            </div>

            <div class="flex items-center">
                <!-- Mobile menu button -->
                <div class="sm:hidden">
                    <button id="mobile-menu-button" type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Hamburger icon -->
                        <svg id="hamburger-icon" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- X icon -->
                        <svg id="close-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Settings Dropdown (Desktop only) -->
                <div class="relative inline-block text-left ml-4 hidden sm:block">
                    <button id="settings-menu" class="text-gray-700 hover:text-gray-900 focus:outline-none p-2">
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <div id="settings-dropdown"
                        class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a wire:navigate href="{{ route('agents.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Agents</a>
                            <a wire:navigate href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('profile.edit') ? 'bg-gray-100' : '' }}">Profile</a>
                            <a wire:navigate href="{{ route('account.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('account.edit') ? 'bg-gray-100' : '' }}">Account</a>
                            @if(auth()->user() && auth()->user()->type === \App\Models\User::TYPE_SUPER_ADMIN)
                            <a wire:navigate href="{{ route('accounts.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('accounts.*') ? 'bg-gray-100' : '' }}">Accounts</a>
                            <a wire:navigate href="{{ route('realtors.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('realtors.*') ? 'bg-gray-100' : '' }}">Realtors</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a wire:navigate href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Dashboard
            </a>
            <a wire:navigate href="{{ route('notices.index') }}"
                class="{{ request()->routeIs('notices.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Notices
            </a>
            <a wire:navigate href="{{ route('tenants.index') }}"
                class="{{ request()->routeIs('tenants.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Tenants
            </a>

            <div class="border-t border-gray-200 pt-4">
                <a wire:navigate href="{{ route('agents.index') }}"
                    class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">Agents</a>
                <a wire:navigate href="{{ route('profile.edit') }}"
                    class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 {{ request()->routeIs('profile.edit') ? 'bg-gray-50 text-gray-800' : '' }}">Profile</a>
                <a wire:navigate href="{{ route('account.edit') }}"
                    class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 {{ request()->routeIs('account.edit') ? 'bg-gray-50 text-gray-800' : '' }}">Account</a>
                @if(auth()->user() && auth()->user()->type === \App\Models\User::TYPE_SUPER_ADMIN)
                <a wire:navigate href="{{ route('accounts.index') }}"
                    class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 {{ request()->routeIs('accounts.*') ? 'bg-gray-50 text-gray-800' : '' }}">Accounts</a>
                <a wire:navigate href="{{ route('realtors.index') }}"
                    class="block pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 {{ request()->routeIs('realtors.*') ? 'bg-gray-50 text-gray-800' : '' }}">Realtors</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left pl-3 pr-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    // Settings dropdown functionality (desktop only)
    document.getElementById('settings-menu').addEventListener('click', function(event) {
        event.stopPropagation();
        document.getElementById('settings-dropdown').classList.toggle('hidden');
    });

    // Mobile menu functionality
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        mobileMenu.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    // Close dropdowns when clicking outside
    window.addEventListener('click', function(event) {
        const settingsDropdown = document.getElementById('settings-dropdown');
        const settingsButton = document.getElementById('settings-menu');

        if (!settingsButton.contains(event.target)) {
            settingsDropdown.classList.add('hidden');
        }
    });
</script>