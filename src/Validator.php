<?php

namespace Bagf\Mailboxlayer;

use GuzzleHttp\Client;

/**
 * Description of Validator
 *
 * @author rory
 */
class Validator
{
    protected $accessKey;
    protected $apiUrl;
    protected $booleans = [];

    public function __construct($accessKey, $https = true)
    {
        $this->accessKey = $accessKey;
        $this->apiUrl = ($https?'https':'http')."://apilayer.net/api/";
    }

    public function validateExtend($attribute, $value, $parameters, $validator)
    {
        foreach ($parameters as $parameter) {
            $method = "must".studly_case($parameter);
            call_user_func([ $this, $method ]);
        }
        
        return $this->validate($value);
    }
    
    public function validate($email)
    {
        $this->client()->get($this->apiUrl, [
            'access_key' => $this->accessKey,
            'email' => $email,
        ]);
    }
    
    public function mustFormatValid()
    {
        $this->booleans['format_valid'] = true;
    }
    
    public function mustMxFound()
    {
        $this->booleans['mx_found'] = true;
    }
    
    public function mustSmtpCheck()
    {
        $this->booleans['smtp_check'] = true;
    }
    
    public function mustCatchAll()
    {
        $this->booleans['catch_all'] = true;
    }
    
    public function mustRole()
    {
        $this->booleans['role'] = true;
    }
    
    public function mustDisposable()
    {
        $this->booleans['disposable'] = true;
    }
    
    public function mustFree()
    {
        $this->booleans['free'] = true;
    }
    
    public function mustNotFormatValid()
    {
        $this->booleans['format_valid'] = false;
    }
    
    public function mustNotMxFound()
    {
        $this->booleans['mx_found'] = false;
    }
    
    public function mustNotSmtpCheck()
    {
        $this->booleans['smtp_check'] = false;
    }
    
    public function mustNotCatchAll()
    {
        $this->booleans['catch_all'] = false;
    }
    
    public function mustNotRole()
    {
        $this->booleans['role'] = false;
    }
    
    public function mustNotDisposable()
    {
        $this->booleans['disposable'] = false;
    }
    
    public function mustNotFree()
    {
        $this->booleans['free'] = false;
    }
    
    protected function client()
    {
        return new Client;
    }
}
