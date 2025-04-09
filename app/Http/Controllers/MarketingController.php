<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\PricingService;

class MarketingController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Display the home page
     */
    public function home()
    {
        return view('marketing.home');
    }

    /**
     * Display the how it works page
     */
    public function howItWorks()
    {
        return view('marketing.how-it-works');
    }

    /**
     * Display the pricing page
     */
    public function pricing()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.pricing', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the FAQ page
     */
    public function faq()
    {
        return view('marketing.faq');
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        return view('marketing.contact');
    }

    /**
     * Send the contact form
     */
    public function sendContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Here you would typically send an email with the contact form data
        // For example:
        // Mail::to('support@oregonpastduerent.com')->send(new \App\Mail\ContactFormSubmission($validated));

        // For now, we'll just redirect with a success message
        return redirect()->route('marketing.contact')->with('success', 'Thank you for your message! We will get back to you as soon as possible.');
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return view('marketing.about');
    }

    /**
     * Display the testimonials page
     */
    public function testimonials()
    {
        return view('marketing.testimonials');
    }

    /**
     * Display the privacy policy page
     */
    public function privacyPolicy()
    {
        return view('marketing.privacy-policy');
    }

    /**
     * Display the terms of service page
     */
    public function terms()
    {
        return view('marketing.terms');
    }

    /**
     * Subscribe to the newsletter
     */
    public function subscribeToNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Here you would typically add the email to your newsletter system
        // For example:
        // Newsletter::subscribe($validated['email']);

        // For now, we'll just redirect with a success message
        return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
