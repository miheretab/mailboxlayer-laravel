<?php

namespace Bagf\Mailboxlayer;

use Illuminate\Support\Collection;

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
        return new Validator(
            $this->accessKey,
            $this->https
        );
    }
    
    public function hasSuggested($email)
    {
        return !empty($this->responses->get(trim($email).".did_you_mean", ''));
    }
    
    public function getSuggestionFor($email)
    {
        return $this->responses->get(trim($email).".did_you_mean", '');
    }
}
