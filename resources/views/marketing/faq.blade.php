@extends('layouts.marketing')

@section('title', 'Oregon Landlord Notice FAQs | Past Due Rent Questions')
@section('description', 'Find answers to common questions about Oregon Past Due Rent notice service, pricing, legal
compliance, and the eviction process for Oregon landlords.')

@section('content')
<!-- Hero Section -->
<section class="bg-indigo-700 py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
            Frequently Asked Questions
        </h1>
        <p class="mt-6 max-w-3xl mx-auto text-xl text-indigo-100">
            Answers to common questions about our service and Oregon's past-due rent notice requirements.
        </p>
    </div>
</section>

<!-- FAQ Categories Section -->
<section class="py-16 bg-white sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-gray-50 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8 rounded-lg shadow-sm">
            <div class="absolute inset-0">
                <div class="bg-white h-1/3 sm:h-2/3"></div>
            </div>
            <div class="relative max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">FAQ Categories</h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                        Browse our frequently asked questions by category
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Category 1: About Our Service -->
                    <div class="flex flex-col h-full rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-600">
                                    <a href="#about-service" class="hover:underline">
                                        About Our Service
                                    </a>
                                </p>
                                <div class="mt-4 space-y-3">
                                    <a href="#about-service" class="block">
                                        <p class="text-base text-gray-900">What exactly does Oregon Past Due Rent do?
                                        </p>
                                    </a>
                                    <a href="#about-service" class="block">
                                        <p class="text-base text-gray-900">Is the service only available in Oregon?</p>
                                    </a>
                                    <a href="#about-service" class="block">
                                        <p class="text-base text-gray-900">How quickly are notices delivered?</p>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-6">
                                <a href="#about-service"
                                    class="text-base font-semibold text-indigo-600 hover:text-indigo-500">
                                    See all questions →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Category 2: Pricing & Payment -->
                    <div class="flex flex-col h-full rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-600">
                                    <a href="#pricing-payment" class="hover:underline">
                                        Pricing & Payment
                                    </a>
                                </p>
                                <div class="mt-4 space-y-3">
                                    <a href="#pricing-payment" class="block">
                                        <p class="text-base text-gray-900">How much does each notice cost?</p>
                                    </a>
                                    <a href="#pricing-payment" class="block">
                                        <p class="text-base text-gray-900">Are there any subscription fees?</p>
                                    </a>
                                    <a href="#pricing-payment" class="block">
                                        <p class="text-base text-gray-900">Do you offer bulk pricing?</p>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-6">
                                <a href="#pricing-payment"
                                    class="text-base font-semibold text-indigo-600 hover:text-indigo-500">
                                    See all questions →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Category 3: Legal & Compliance -->
                    <div class="flex flex-col h-full rounded-lg shadow-lg overflow-hidden">
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-indigo-600">
                                    <a href="#legal-compliance" class="hover:underline">
                                        Legal & Compliance
                                    </a>
                                </p>
                                <div class="mt-4 space-y-3">
                                    <a href="#legal-compliance" class="block">
                                        <p class="text-base text-gray-900">Are your notices legally compliant?</p>
                                    </a>
                                    <a href="#legal-compliance" class="block">
                                        <p class="text-base text-gray-900">What's the difference between 10-day and
                                            13-day notices?</p>
                                    </a>
                                    <a href="#legal-compliance" class="block">
                                        <p class="text-base text-gray-900">Can I use this service to start an eviction?
                                        </p>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-6">
                                <a href="#legal-compliance"
                                    class="text-base font-semibold text-indigo-600 hover:text-indigo-500">
                                    See all questions →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Detailed Categories -->
<section class="py-16 bg-gray-50 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- About Our Service -->
        <div id="about-service" class="py-12 border-b border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">
                About Our Service
            </h2>
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What exactly does Oregon Past Due Rent do?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Oregon Past Due Rent helps landlords and property managers issue legally compliant "Notice of
                        Termination for Nonpayment of Rent" documents to tenants who are late on rent. We handle the
                        entire process - from creating the proper notice to delivering it according to Oregon law, and
                        providing proof of service.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Is the service only available in Oregon?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Yes, currently our service is specifically designed for Oregon landlords and property managers,
                        as rental laws vary significantly by state. Our notices comply with Oregon's specific
                        requirements, including the 2023 law updates.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How quickly are notices delivered?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Notices received before 1pm PST Monday through Friday will be served the same day. Otherwise,
                        notices will be served on the next business day. We do not deliver on weekends or Oregon
                        holidays.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Who uses Oregon Past Due Rent?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Our service is used by both individual landlords with a single rental property and large
                        property management companies with multiple units. Anyone who needs to issue past-due rent
                        notices in Oregon can benefit from our service.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What information do I need to provide?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        You'll need to provide basic information about the tenant (name, rental address), details about
                        the past-due rent (amount, due date), and your contact information. Our simple form guides you
                        through all the required fields.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How do you serve the notices?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        We use multiple methods in accordance with Oregon law, which may include personal service,
                        posting at the property, and mailing. This comprehensive approach ensures that your notice meets
                        all legal requirements for service.
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Pricing & Payment -->
        <div id="pricing-payment" class="py-12 border-b border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">
                Pricing & Payment
            </h2>
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How much does each notice cost?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Each notice costs ${{ $standardPrice }}. This flat fee covers the entire process from notice
                        creation to delivery and proof of service.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Are there any subscription fees?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        No, there are absolutely no subscription fees or recurring charges. You only pay when you need
                        to send a notice.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Do you offer bulk pricing?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Yes, we offer special bulk pricing for property managers who send 10 or more notices per month.
                        Please <a href="{{ route('marketing.contact') }}?subject=Bulk%20Pricing"
                            class="text-indigo-600 hover:text-indigo-500">contact us</a> for a custom quote based on
                        your volume needs.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What payment methods do you accept?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        We accept all major credit cards and debit cards. For property management companies with bulk
                        needs, we can also arrange for invoicing.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Are there any hidden fees?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        No, the ${{ $standardPrice }} per notice fee is all-inclusive. The only potential additional
                        cost would be for optional notarized affidavits, which can be requested for a small additional
                        fee if needed for court proceedings.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How do I pay for notices?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Payment is collected at the time you submit a notice request through our secure online payment
                        system. Your payment information is securely stored to make future notices even easier to
                        process.
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Legal & Compliance -->
        <div id="legal-compliance" class="py-12">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">
                Legal & Compliance
            </h2>
            <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-12">
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Are your notices legally compliant?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        Yes, our notices are fully compliant with Oregon law, including the recent 2023 updates. Our
                        templates have been reviewed by legal professionals and have withstood scrutiny in eviction
                        courts throughout Oregon.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What's the difference between 10-day and 13-day notices?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        A 10-day notice is used for standard rental situations, giving tenants 10 days to pay past-due
                        rent before eviction proceedings can begin. A 13-day notice is required for certain subsidized
                        housing situations. Our system helps determine which notice is appropriate for your specific
                        situation.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Can I use this service to start an eviction?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        This service provides the legally required first step in the eviction process. A properly served
                        "Notice of Termination for Nonpayment of Rent" is required before you can file for eviction in
                        Oregon. After the notice period expires, if the tenant hasn't paid, you can proceed with filing
                        an eviction case in court.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        Will the notice guarantee I can evict my tenant?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        While our service ensures that the notice and service comply with Oregon law, eviction
                        proceedings involve additional legal requirements and court proceedings. However, a properly
                        served notice significantly increases your chances of a successful eviction case if it becomes
                        necessary.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        What proof of service do you provide?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        We provide detailed documentation of when and how the notice was delivered, including dates,
                        times, and methods. Upon request, we can provide a notarized affidavit of service that can be
                        used in court proceedings.
                    </dd>
                </div>

                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">
                        How long must I wait after serving a notice before I can file for eviction?
                    </dt>
                    <dd class="mt-2 text-base text-gray-500">
                        You must wait for the notice period to expire (10 or 13 days, depending on the notice type). If
                        the tenant hasn't paid the past-due rent or moved out by that time, you can proceed with filing
                        an eviction case in court.
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</section>

<!-- Still Have Questions Section -->
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-12 md:py-20 bg-indigo-600 rounded-lg shadow-xl overflow-hidden">
            <div class="px-6 md:px-12 lg:px-16">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Still have questions?
                    </h2>
                    <p class="mt-4 text-lg leading-6 text-indigo-100">
                        We're here to help. Reach out to our team for answers to any questions not covered here.
                    </p>
                    <div class="mt-8 flex justify-center gap-x-4">
                        <a href="{{ route('marketing.contact') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 shadow-sm">
                            Contact Us
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-800 hover:bg-indigo-700 shadow-sm">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection