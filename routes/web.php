<?php

use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Notices\Index as NoticesIndex;
use App\Livewire\Notices\Create as NoticesCreate;
use App\Livewire\Tenants\Index as TenantsIndex;
use App\Livewire\Tenants\Create as TenantsCreate;
use App\Livewire\Tenants\Edit as TenantsEdit;
use App\Livewire\Agents\Index as AgentsIndex;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    // Index route - Coming Soon
    Route::get('/', function () {
        return view('welcome')->with('message', 'Coming Soon');
    })->name('home');

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
    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard Route
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    // Notice Routes
    Route::get('notices', NoticesIndex::class)->name('notices.index');
    Route::get('notices/create', NoticesCreate::class)->name('notices.create');
    Route::get('notices/{notice}', App\Livewire\Notices\Show::class)->name('notices.show');
    Route::get('notices/{notice}/edit', App\Livewire\Notices\Edit::class)->name('notices.edit');

    // Tenant Routes
    Route::get('tenants', TenantsIndex::class)->name('tenants.index');
    Route::get('tenants/create', TenantsCreate::class)->name('tenants.create');
    Route::get('tenants/{tenant}/edit', TenantsEdit::class)->name('tenants.edit');

    // Agent Routes
    Route::get('agents', AgentsIndex::class)->name('agents.index');
    Route::get('agents/create', App\Livewire\Agents\Create::class)->name('agents.create');
    Route::get('agents/{agent}/edit', App\Livewire\Agents\Edit::class)->name('agents.edit');
});
