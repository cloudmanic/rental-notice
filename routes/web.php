<?php

use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Notices\Index as NoticesIndex;
use App\Livewire\Notices\Create as NoticesCreate;
use App\Livewire\Notices\Preview as NoticesPreview;
use App\Http\Controllers\NoticeController;
use App\Livewire\Tenants\Index as TenantsIndex;
use App\Livewire\Tenants\Create as TenantsCreate;
use App\Livewire\Tenants\Edit as TenantsEdit;
use App\Livewire\Agents\Index as AgentsIndex;
use App\Livewire\Profile\Edit as ProfileEdit;
use App\Livewire\Account\Edit as AccountEdit;
use App\Livewire\Accounts\Index as AccountsIndex;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\Auth\AccountImpersonationController;
use \App\Http\Controllers\StripeCheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Marketing Routes
Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::get('/how-it-works', [MarketingController::class, 'howItWorks'])->name('marketing.how-it-works');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/contact', [MarketingController::class, 'sendContactForm'])->name('marketing.contact.send');
Route::get('/about', [MarketingController::class, 'about'])->name('marketing.about');
Route::get('/testimonials', [MarketingController::class, 'testimonials'])->name('marketing.testimonials');
Route::get('/privacy-policy', [MarketingController::class, 'privacyPolicy'])->name('marketing.privacy-policy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('marketing.terms');
Route::get('/refund-policy', [MarketingController::class, 'refundPolicy'])->name('marketing.refund-policy');
Route::post('/newsletter/subscribe', [MarketingController::class, 'subscribeToNewsletter'])->name('marketing.newsletter.subscribe');

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'show')->name('login');
        Route::post('login', 'authenticate');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'show')->name('register');
        Route::post('register', 'register');
    });

    // Password Reset Routes
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('slack-test', function (Illuminate\Http\Request $request) {
        $user = $request->user();
        $notice = \App\Models\Notice::find(1);

        $user->notify(new App\Notifications\NoticePaid($notice));

        //\Illuminate\Support\Facades\Mail::to($request->user())->send(new \App\Mail\NoticePaid());

        return 'woots';
    })->name('slack-test');


    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard Route
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    // Profile Route
    Route::get('/profile', ProfileEdit::class)->name('profile.edit');

    // Account Route
    Route::get('/account', AccountEdit::class)->name('account.edit');

    // Super Admin only routes
    Route::middleware('superadmin')->group(function () {
        // Accounts management
        Route::get('/accounts', AccountsIndex::class)->name('accounts.index');

        // Account impersonation
        Route::get('/impersonate/{user}', [AccountImpersonationController::class, 'impersonate'])->name('impersonate');
    });

    // Leave impersonation route (available to any impersonating user)
    Route::get('/leave-impersonation', [AccountImpersonationController::class, 'leave'])->name('leave-impersonation');

    // Notice Routes
    Route::get('notices', NoticesIndex::class)->name('notices.index');
    Route::get('notices/create', NoticesCreate::class)->name('notices.create');
    Route::get('notices/{notice}', App\Livewire\Notices\Show::class)->name('notices.show');
    Route::get('notices/{notice}/edit', App\Livewire\Notices\Edit::class)->name('notices.edit');
    Route::get('notices/{notice}/preview', NoticesPreview::class)->name('notices.preview');
    Route::get('notices/{notice}/pdf', [NoticeController::class, 'generatePdf'])->name('notices.pdf');
    Route::get('notices/{notice}/shipping-form', [NoticeController::class, 'generateShippingForm'])->name('notices.shipping-form');
    Route::get('notices/{notice}/complete-package', [NoticeController::class, 'generateCompletePackage'])->name('notices.complete-package');

    // Tenant Routes
    Route::get('tenants', TenantsIndex::class)->name('tenants.index');
    Route::get('tenants/create', TenantsCreate::class)->name('tenants.create');
    Route::get('tenants/{tenant}/edit', TenantsEdit::class)->name('tenants.edit');

    // Agent Routes
    Route::get('agents', AgentsIndex::class)->name('agents.index');
    Route::get('agents/create', App\Livewire\Agents\Create::class)->name('agents.create');
    Route::get('agents/{agent}/edit', App\Livewire\Agents\Edit::class)->name('agents.edit');

    // Stripe Checkout Routes
    Route::get('/stripe/checkout/success', [StripeCheckoutController::class, 'success'])->name('stripe.checkout.success');
    Route::get('/stripe/checkout/cancel', [StripeCheckoutController::class, 'cancel'])->name('stripe.checkout.cancel');
    Route::get('/stripe/checkout/{notice}', [StripeCheckoutController::class, 'create'])->name('stripe.checkout');
});
