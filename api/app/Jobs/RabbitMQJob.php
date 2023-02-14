<?php

namespace App\Jobs;

use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

class RabbitMQJob extends BaseJob
{
    public function fire(): void
    {
        ($this->instance = $this->resolve(ImportTest::class))->handle($this->payload());

        $this->delete();
    }

    public function payload()
    {
        return [
            'job'  => 'App\Jobs\ImportTest@handle',
            'data' => $this->getRawBody()
        ];
    }
}
