<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LogLevel;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register():void
    {
        $this->reportable(function (\Throwable $e) {
            $this->sendBot($e);
        });
    }

    public function sendBot(\Throwable $e): void
    {
        try {
            $queueClient = Redis::connection('bot')->client();

            if (LogLevel::ERROR == Arr::first($this->levels, fn ($level, $type) => $e instanceof $type, LogLevel::ERROR)) {
                $queueClient->publish('bot:error', json_encode([
                    'file' => $e->getFile() . ' (' . $e->getLine() . ')',
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
            }
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
