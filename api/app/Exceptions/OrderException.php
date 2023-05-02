<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Redis;

class OrderException extends \DomainException
{
    protected int $orderId;

//    public function __construct(int $orderId, string $message = "", ?\Throwable $previous = null)
//    {
//        $this->orderId = $orderId;
//        parent::__construct($message, 0, $previous);
//    }

//    public function report(): void
//    {
//        $redis = Redis::connection('bot')->client();
//        $redis->publish('bot:order', json_encode([
//            'file' => $e->getFile() . ' (' . $e->getLine() . ')',
//            'message' => $e->getMessage()
//        ], JSON_UNESCAPED_UNICODE));
//    }
}
