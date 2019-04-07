<?php

namespace Ibca\Mudu;

use Illuminate\Support\ServiceProvider;

class MuduServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mudu.php' => config_path('mudu.php'),
            ], 'config');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('mudu', function ($app) {
            $access_token = config('mudu.access_token');

            return new MuduAPI($access_token);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mudu'];
    }
}
