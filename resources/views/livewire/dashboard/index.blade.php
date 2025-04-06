<div>
    <h3 class="text-lg leading-6 font-medium text-gray-900">Latest Activity</h3>

    <div class="border-t border-gray-200 mt-4">
        @if ($activities->count() > 0)
        <div class="overflow-hidden">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($activities as $activity)
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center space-x-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $activity->user ? $activity->user->name : '' }}
                                </p>
                                <!-- Activity Type Subtitle -->
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $activity->type === 'Tenant' ? 'bg-blue-100 text-blue-800' : 
                                   ($activity->type === 'Notice' ? 'bg-green-100 text-green-800' : 
                                   ($activity->type === 'Agent' ? 'bg-purple-100 text-purple-800' : 
                                   ($activity->type === 'System' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    {{ $activity->type }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 truncate">
                                @php
                                    $description = $activity->description;
                                    // For agents with placeholder - we check both event type and description pattern
                                    if ($activity->type === 'Agent' && strpos($description, '{name}') !== false) {
                                        if ($activity->agent_id && $activity->agent) {
                                            // If agent relationship exists, use it
                                            $description = str_replace('{name}', $activity->agent->name, $description);
                                        } else {
                                            // If the agent record is gone, just remove the placeholder
                                            // Note: For deleted agents we already put the name in the description
                                            $description = str_replace('{name}', '', $description);
                                        }
                                    }
                                @endphp
                                {{ $description }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
            {{ $activities->links() }}
        </div>
        @else
        <!-- First Run Experience -->
        <div class="px-4 py-8 text-center bg-white rounded-lg shadow-sm">
            <div class="mx-auto max-w-lg">
                <svg class="mx-auto h-16 w-16 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>

                <h3 class="mt-4 text-lg font-bold text-gray-900">Get Started with Your First Late Rent Notice</h3>

                <div class="mt-6">
                    <a href="{{ route('notices.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create My First Late Rent Notice
                    </a>
                </div>

                <p class="mt-4 text-sm text-gray-500">
                    Once you give us a little information about your tenant, <br /> we will send a court-tested demand
                    for rent notice.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>