<?php

namespace App\Console\Commands\Import;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command as BaseCommand;
use Illuminate\Support\Facades\Redis;

class Command extends BaseCommand
{
    protected $name = 'import';
    protected array $config;
    protected Client $client;
    protected Redis $redis;
    protected ?Carbon $startTime = null;

    public function __construct()
    {
        $this->config = config('data.1c');
        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'auth' => [$this->config['login'], $this->config['password']],
            'http_errors' => false,
            'verify' => false
        ]);
        $this->redis = Redis::connection('bot')->client();

        parent::__construct();
    }

    protected function getData(int $url = 0): \SimpleXMLElement
    {
        $response = $this->client->get($this->config['urls'][$url]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения данных');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        return $xml;
    }
}