<?php

namespace Bagf\Mailboxlayer;

use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mailboxlayer', '\Bagf\Mailboxlayer\ValidatorFacade@validateExtend');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('mailboxlayer.validator', function ($app) {
            $config = $app['config'];
            return new Validator(
                $config->get('services.mailboxlayer.access_key'),
                $config->get('services.mailboxlayer.https', true)
            );
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mailboxlayer.validator'];
    }
}