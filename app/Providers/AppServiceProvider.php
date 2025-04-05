<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\NoticeType;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register the public path for Vite
        $this->app->bind('path.public', function () {
            return base_path('public');
        });

        // Set the most recent notice type plan date for new accounts
        Account::creating(function ($account) {
            if (!$account->notice_type_plan_date) {
                $account->notice_type_plan_date = NoticeType::getMostRecentPlanDate();
            }
        });
    }
}
