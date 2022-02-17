<?php

namespace App\Providers;

use App\HttpClients\DCFClient;
use App\HttpClients\SFCClient;
use App\HttpClients\SSVClient;
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
        $this->app->singleton(SFCClient::class, function ($app) {
            return new SFCClient();
        });

        $this->app->singleton(DCFClient::class, function ($app) {
            return new DCFClient();
        });

        $this->app->singleton(SSVClient::class, function ($app) {
            return new SSVClient();
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
