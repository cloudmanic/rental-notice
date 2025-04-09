@extends('layouts.marketing')

@section('title', 'Refund Policy - Oregon Past Due Rent')
@section('description', 'Refund Policy for Oregon Past Due Rent, a service of Cloudmanic Labs, LLC.')

@section('content')
<div class="bg-gradient-to-b from-white to-gray-50">
    <div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:py-20 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
                <span class="block">Refund Policy</span>
            </h1>
            <p class="mt-4 text-xl text-indigo-600 font-medium">Last Updated: April 9, 2025</p>
        </div>

        <div class="mt-16 bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-8 sm:p-10">
                <div class="prose prose-indigo prose-lg max-w-none">
                    <div class="p-6 mb-8 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                        <p class="text-lg font-medium text-indigo-800">
                            Thank you for choosing Oregon Past Due Rent. Please review our refund policy below.
                        </p>
                    </div>

                    <section class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">All Sales Are Final</h2>
                        <p class="mb-4">All purchases made through Oregon Past Due Rent are final. Due to the immediate processing nature of our document preparation and delivery services, we do not offer refunds once a notice has been submitted for processing.</p>

                        <p class="mb-4">When you submit a notice through our system and complete payment, our team immediately begins the document preparation process, which includes:</p>

                        <ul class="list-disc pl-6 space-y-2 mt-4 mb-4">
                            <li class="text-gray-800">Document verification and formatting</li>
                            <li class="text-gray-800">Legal compliance review</li>
                            <li class="text-gray-800">Printing services</li>
                            <li class="text-gray-800">Mailing and delivery preparation</li>
                        </ul>

                        <p>Because these services begin promptly upon payment, we are unable to provide refunds.</p>
                    </section>

                    <section class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Service Errors</h2>
                        <p>In the rare event that our service fails to perform as described due to an error on our part, please contact our customer support team at <a href="mailto:support@oregonpastduerent.com" class="text-indigo-600 hover:text-indigo-500">support@oregonpastduerent.com</a> immediately. We will work with you to resolve the issue, which may include re-processing your notice at no additional charge.</p>
                    </section>

                    <section class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
                        <p>If you have any questions about our refund policy, please contact us at:</p>
                        <div class="mt-4">
                            <p><strong>Email:</strong> <a href="mailto:support@oregonpastduerent.com" class="text-indigo-600 hover:text-indigo-500">support@oregonpastduerent.com</a></p>
                            <p><strong>Phone:</strong> (503) 555-0123</p>
                        </div>
                    </section>

                    <div class="mt-12 p-6 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center">
                            <div class="h-12 w-12 bg-indigo-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-lg">CL</span>
                            </div>
                            <div class="ml-4">
                                <p class="text-xl font-bold text-gray-900">Cloudmanic Labs, LLC</p>
                                <p class="text-gray-600">Newberg, Oregon</p>
                                <p class="text-gray-500 text-sm mt-2">Last Updated: April 9, 2025</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('marketing.contact') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Contact Us
            </a>
        </div>
    </div>
</div>
@endsection