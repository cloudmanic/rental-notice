@props([
    'title' => 'Watch Our Quick Tutorial',
    'subtitle' => 'See How It Works',
    'description' => 'Learn how our simple process helps Oregon landlords serve legally compliant past-due rent notices in just a few minutes.',
    'videoId' => 'Jq11xBUVoqg',
    'trackingLocation' => 'Home Page'
])

<!-- Video Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">{{ $subtitle }}</h2>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">
                {{ $title }}
            </p>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                {{ $description }}
            </p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <div class="relative rounded-lg shadow-lg overflow-hidden cursor-pointer" style="padding-bottom: 56.25%;" onclick="openVideoModal()">
                <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" alt="Oregon Past Due Rent Notice Video Tutorial" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="inline-flex items-center justify-center p-4 bg-white rounded-full shadow-xl transform transition-transform hover:scale-110">
                        <svg class="h-10 w-10 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Modal -->
<div id="videoModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-75 transition-opacity" onclick="closeVideoModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <button onclick="closeVideoModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors z-10">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="relative pt-[56.25%]">
                <iframe id="youtubeVideo" 
                    class="absolute inset-0 w-full h-full rounded-lg"
                    src="" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function openVideoModal() {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('youtubeVideo');
        
        // Track video open event in Plausible
        if (typeof plausible !== 'undefined') {
            plausible('how-to-video-opened', {
                props: {
                    location: '{{ $trackingLocation }}',
                    video: 'Tutorial Video'
                }
            });
        }
        
        // Set the video source
        iframe.src = 'https://www.youtube.com/embed/{{ $videoId }}?si=DoIbI0jqCIfZcKAq&autoplay=1';
        
        // Show the modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const iframe = document.getElementById('youtubeVideo');
        
        // Stop the video
        iframe.src = '';
        
        // Hide the modal
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeVideoModal();
        }
    });
</script>