<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.home', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the how it works page
     */
    public function howItWorks()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.how-it-works', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
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
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.faq', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.contact', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
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
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.about', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the privacy policy page
     */
    public function privacyPolicy()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.privacy-policy', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the terms of service page
     */
    public function terms()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.terms', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }

    /**
     * Display the refund policy page
     */
    public function refundPolicy()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.refund-policy', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
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

    /**
     * Display the sitemap page
     */
    public function sitemap()
    {
        $standardPrice = $this->pricingService->getStandardPrice();
        $bulkPrices = $this->pricingService->getBulkPrices();

        return view('marketing.sitemap', [
            'standardPrice' => $standardPrice,
            'bulkPrices' => $bulkPrices,
        ]);
    }
}
