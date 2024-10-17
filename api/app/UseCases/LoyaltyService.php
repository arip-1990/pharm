<?php

namespace App\UseCases;

use GuzzleHttp\Client;

abstract class LoyaltyService
{
    protected Client $client;
    protected array $urls;
    protected array $config;

    public function __construct(bool $pos = false) {
        $this->urls = config('data.loyalty.url');
        $this->config = config('data.loyalty.prod');
//        $this->config = config('app.env') === 'production' ? config('data.loyalty.prod') : config('data.loyalty.test');

        $headers = $pos ? ['Content-Type' => 'text/xml; charset=utf-8', 'SOAPAction' => 'http://loyalty.manzanagroup.ru/loyalty.xsd/ProcessRequest'] : ['Content-Type' => 'application/json; charset=utf-8'];
        $this->client = new Client([
            'headers' => $headers,
            'auth' => $pos ? [$this->config['login'], $this->config['password']] : [],
            'http_errors' => false,
            'verify' => false
        ]);
    }
}
