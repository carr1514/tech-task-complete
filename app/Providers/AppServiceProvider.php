<?php

namespace App\Providers;

use App\Services\LookupServiceInterface;
use App\Services\MinecraftLookupService;
use App\Services\SteamLookupService;
use App\Services\XblLookupService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minecraft', function ($app)
        {
            return new MinecraftLookupService(new Client());
        });

        $this->app->singleton('steam', function ($app)
        {
            return new SteamLookupService(new Client());
        });

        $this->app->singleton('xbl', function ($app)
        {
            return new XblLookupService(new Client());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
