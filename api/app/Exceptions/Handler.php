<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
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
            $connection = Queue::connection();
            if (LogLevel::ERROR == Arr::first($this->levels, fn ($level, $type) => $e instanceof $type, LogLevel::ERROR)) {
                $connection->pushRaw(json_encode([
                    'type' => 'error',
                    'data' => [
                        'file' => $e->getFile() . ' (' . $e->getLine() . ')',
                        'message' => $e->getMessage()
                    ]
                ]), 'bot');
            }
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
