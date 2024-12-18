<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\HelpDeskServiceInterface;

use App\Services\helpdeskServices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Form Services Interfaces
        $this->app->bind(HelpDeskServiceInterface::class, helpdeskServices::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
