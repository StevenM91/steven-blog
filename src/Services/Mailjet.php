<?php

namespace app\Services;


class Mailjet
{
    private $apiKey;
    private $apiSecretKey;

    public function __construct($apiKey, $apiSecretKey)
    {
        $this->apiKey = $apiKey;
        $this->apiSecretKey = $apiSecretKey;
    }
}