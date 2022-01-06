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
        $data = simplexml_load_file('http://' . $this->config['user'] . ':' . $this->config['password'] . '@' . $this->config['urls'][$url]);
        if ($data === false)
            throw new \RuntimeException('Ошибка получения данных!');

        return $data;
    }
}
