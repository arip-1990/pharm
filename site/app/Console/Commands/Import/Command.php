<?php

namespace App\Console\Commands\Import;

use Carbon\Carbon;
use Illuminate\Console\Command as BaseCommand;

class Command extends BaseCommand
{
    protected $name = 'import';
    protected array $config;
    protected Carbon $startTime;

    public function __construct()
    {
        $this->config = config('data.1c');
        $this->startTime = Carbon::now();
        parent::__construct();
    }

    protected function getData(int $url = 0): \SimpleXMLElement
    {
        $ch = curl_init('http://' . $this->config['user'] . ':' . $this->config['password'] . '@' . $this->config['urls'][$url]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = simplexml_load_string($data);
        if ($data === false)
            throw new \RuntimeException('Ошибка получения данных!');

        return $data;
    }
}
