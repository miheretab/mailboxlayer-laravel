<?php

namespace Bagf\Mailboxlayer;

use Illuminate\Support\ServiceProvider as ServiceProviderContract;
use Bagf\Mailboxlayer\ValidatorFacade as MailboxlayerFacade;
use Validator;

class ServiceProvider extends ServiceProviderContract
{
    protected $emails = [];
    
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mailboxlayer', function ($attribute, $value, $parameters, $validator) {
            $pass = MailboxlayerFacade::make()
                ->validateExtend($attribute, $value, $parameters, $validator);
            
            if (!$pass) {
                $this->emails[count($this->emails)] = $value;
            }
            
            return $pass;
        });
        
        Validator::replacer('mailboxlayer', function($message, $attribute, $rule, $parameters) {
            if (!isset($this->emails[0])) {
                return str_replace(':suggestion', '', $message);
            }
            
            $email = trim($this->emails[0]);
            
            unset($this->emails[0]);
            $this->emails = array_values($this->emails);
            
            if (!MailboxlayerFacade::hasSuggested($email)) {
                return str_replace(':suggestion', '', $message);
            }
            
            $suggestion = MailboxlayerFacade::getSuggestionFor($email);
            $suggestText = trans('validation.mailboxlayer_suggest', ['email' => $suggestion]);
            return str_replace(':suggestion', $suggestText, $message);
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