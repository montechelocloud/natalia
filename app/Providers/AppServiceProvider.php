<?php

namespace App\Providers;

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
        $baseUrl = env('SFC_ENDPOINT');
        $this->app->singleton(Client::class, function ($app) use ($baseUrl)
        {
            return new Client([
                'base_uri' => $baseUrl,
                'http_errors' => false,
                'headers' => [
                    'Cache-Control' => 'no-cache',
                    'Accept' => 'aplication/json',
                    'Content-type' => 'aplication/json',
                    'Lenguage' => 'es-CO',
                    'X-SFC-Signature' => '' 
                ]
            ]);
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
