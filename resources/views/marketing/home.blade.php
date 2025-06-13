@extends('layouts.marketing')

@section('title', 'Oregon Past Due Rent Notices | Legal Landlord Service')
@section('description', 'Oregon Past Due Rent helps landlords issue legally compliant past-due rent notices with
professional delivery for just $' . $standardPrice . ' per notice.')

@section('content')
<!-- Hero Section -->
<x-marketing.hero 
    title="Oregon Past Due Rent Notices <br class='hidden sm:block'>Made Simple"
    description="Issue legally compliant 10-day or 13-day nonpayment notices with professional delivery for just ${{ $standardPrice }} per notice. Designed specifically for Oregon landlords and property managers."
/>

<!-- Video Section -->
<x-marketing.video-tutorial trackingLocation="Home Page" />

<!-- Features Section -->
<x-marketing.features-section 
    title="Why Choose Us"
    subtitle="The Simplest Way to Serve Rent Notices"
    description="Save time and money while ensuring your notices are legally compliant with Oregon's strict requirements."
    :price="$standardPrice"
/>

<!-- How It Works Section -->
<x-marketing.how-it-works 
    title="Process"
    description="Three simple steps to issue legally compliant past-due rent notices"
    :price="$standardPrice"
/>

<!-- Testimonials Section -->
<x-marketing.testimonials-section />

<!-- Pricing CTA -->
<x-marketing.cta-section 
    title="<span class='block'>Ready to get started?</span><span class='block text-indigo-200'>Just ${{ $standardPrice }} per notice with no subscription fees.</span>"
    description="Join hundreds of Oregon landlords who trust us for their past-due rent notices."
    backgroundColor="bg-indigo-800"
/>

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
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" 
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
                    location: 'Home Page',
                    video: 'Tutorial Video'
                }
            });
        }
        
        // Set the video source
        iframe.src = 'https://www.youtube.com/embed/Jq11xBUVoqg?si=DoIbI0jqCIfZcKAq&autoplay=1';
        
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
@endsection