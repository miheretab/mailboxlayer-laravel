<?php

namespace Bagf\Mailboxlayer;

use GuzzleHttp\Client;
use ArrayAccess;
use ErrorException;
use Illuminate\Support\Str;

class Validator
{
    protected $accessKey;
    protected $apiUrl;
    protected $booleans = [];
    /**
     * @var ArrayAccess
     */
    protected $responseHistory;

    public function __construct($accessKey, $https = true)
    {
        $this->accessKey = $accessKey;
        $this->apiUrl = ($https?'https':'http')."://apilayer.net/api/check";
    }
    
    public function setResponseHistory(ArrayAccess &$history)
    {
        $this->responseHistory = $history;
    }

    public function validateExtend($attribute, $value, $parameters, $validator)
    {
        foreach ($parameters as $parameter) {
            $method = "must".Str::studly_case($parameter);
            call_user_func([ $this, $method ]);
        }
        
        return $this->validate(strtolower(trim($value)));
    }
    
    public function validate($email)
    {
        if (!count($this->booleans)) {
            return true;
        }
        
        $response = $this->resolveResponse($email);
        
        // if the error property isnt set it implys successful response
        if (array_get($response, 'success', true) !== true) {
            $code = array_get($response, 'error.code');
            $info = array_get($response, 'error.info');
            
            throw new ErrorException("Mailboxlayer API: Error code {$code} {$info}");
        }
        
//        $theirEmail = strtolower(array_get($response, 'email', ''));
//        if ($theirEmail !== $email) {
//            throw new ErrorException("Mailboxlayer API: Incorrect address received. Sent {$email} but confirmed {$theirEmail}");
//        }
        
        foreach ($this->booleans as $key => $bool) {
            if (!isset($response[$key])) {
                throw new ErrorException("Mailboxlayer API: Response is lacking property {$key}");
            }
            
            if (is_null($response[$key])) {
                /**
                 * @todo Configure default action if null is returned, for now we fail the validation
                 * if a property is required but not returned (i.e. null)
                 */
                return false;
            }
            
            if ($response[$key] !== $bool) {
                return false;
            }
        }
        
        return true;
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
    
    public function checkSmtp()
    {
        $smtpChecks = [
            'mx_found',
            'smtp_check',
            'catch_all',
        ];
        
        foreach ($smtpChecks as $check) {
            if (isset($this->booleans[$check])) {
                return true;
            }
        }
        
        return false;
    }
    
    public function makeRequest($email)
    {
        $query = [
            'access_key' => $this->accessKey,
            'email' => $email,
        ];
        
        if (!$this->checkSmtp()) {
            $query['smtp'] = '0';
        }
        
        $response = $this->client()->get($this->apiUrl, compact('query'));
        $json = $response->getBody()."";
        $decoded = json_decode($json, true);
        
        if (!is_array($decoded)) {
            throw new ErrorException("Mailboxlayer API: Failed to decode response ({$json}");
        }
        
        return $decoded;
    }
    
    protected function resolveResponse($email)
    {
        if (is_null($this->responseHistory)) {
            return $this->makeRequest($email);
        }
        
        if ($this->responseHistory->offsetExists($email)) {
            return $this->responseHistory->offsetGet($email);
        } else {
            $response = $this->makeRequest($email);
            $this->responseHistory->offsetSet($email, $response);
            return $response;
        }
    }
}
