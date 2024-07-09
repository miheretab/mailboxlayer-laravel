<?php

namespace Bagf\Mailboxlayer;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class ValidatorFactory
{
    protected $accessKey;
    protected $https;
    protected $responses;


    public function __construct($accessKey, $https = true)
    {
        $this->accessKey = $accessKey;
        $this->https = $https;
        $this->responses = new Collection;
    }

    public function make()
    {
        $validator = new Validator(
            $this->accessKey,
            $this->https
        );
        
        $validator->setResponseHistory($this->responses);
        
        return $validator;
    }
    
    public function hasSuggested($email)
    {
        return !empty(Arr::get($this->responses->get(trim($email)), 'did_you_mean', ''));
    }
    
    public function getSuggestionFor($email)
    {
        return Arr::get($this->responses->get(trim($email)), 'did_you_mean', '');
    }
}
