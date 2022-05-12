<?php

namespace App\Providers;

use App\Http\Clients\DCFClient;
use App\Http\Clients\SSVClient;
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
