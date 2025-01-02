<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\HelpDeskServiceInterface;
use App\Contracts\AuthServicesInterface;
use App\Contracts\XmlServicesInterface;

use App\Repositories\GlpiUserRepository;

use App\Models\GlpiUser;

use App\Services\AuthServices;
use App\Services\helpdeskServices;
use App\Services\XmlServices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Form Services Interfaces
        $this->app->bind(HelpDeskServiceInterface::class, helpdeskServices::class);
        $this->app->bind(XmlServicesInterface::class, XmlServices::class);
        //API Interfaces
        $this->app->bind(AuthServicesInterface::class, AuthServices::class);
        //GlpiUserRepository
        $this->app->singleton(GlpiUserRepository::class, function ($app) {
            return new GlpiUserRepository(new GlpiUser());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
