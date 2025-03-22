<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Contracts\AuthServicesInterface;
use App\Contracts\CastServicesInterface;
use App\Contracts\HelpDeskServicesInterface;
use App\Contracts\XmlServicesInterface;
use App\Contracts\SoftlogyDeskServicesInterface;

use App\Repositories\GlpiUserRepository;

use App\Models\GlpiUser;

use App\Services\AuthServices;
use App\Services\CastServices;
use App\Services\helpdeskServices;
use App\Services\XmlServices;
use App\Services\SoftlogyDeskServices;

use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Form Services Interfaces
        $this->app->bind(HelpDeskServicesInterface::class, helpdeskServices::class);
        $this->app->bind(XmlServicesInterface::class, XmlServices::class);
        $this->app->bind(CastServicesInterface::class, CastServices::class);
        $this->app->bind(SoftlogyDeskServicesInterface::class, SoftlogyDeskServices::class);
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
        Blade::component('components.layout.app', 'app-layout');
    }
}
