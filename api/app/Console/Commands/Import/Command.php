<?php

namespace App\Console\Commands\Import;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command as BaseCommand;

class Command extends BaseCommand
{
    protected $name = 'import';
    protected array $config;
    protected Carbon $startTime;
    protected Client $client;

    public function __construct()
    {
        $this->config = config('data.1c');
        $this->startTime = Carbon::now();
        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'auth' => [$this->config['login'], $this->config['password']],
            'http_errors' => false,
            'verify' => false
        ]);
        parent::__construct();
    }

    protected function getData(int $url = 0): \SimpleXMLElement
    {
        $response = $this->client->get($this->config['urls'][$url]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        return $xml;
    }
}
