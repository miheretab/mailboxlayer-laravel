<?php

namespace Bagf\Mailboxlayer;

use Illuminate\Support\ServiceProvider as ServiceProviderContract;
use Bagf\Mailboxlayer\ValidatorFacade as MailboxlayerFacade;
use Validator;

class ServiceProvider extends ServiceProviderContract
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mailboxlayer', function ($attribute, $value, $parameters, $validator) {
            return MailboxlayerFacade::make()
                ->validateExtend($attribute, $value, $parameters, $validator);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mailboxlayer', function ($app) {
            $config = $app['config'];
            return new ValidatorFactory(
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
        return ['mailboxlayer'];
    }
}