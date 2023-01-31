<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LogLevel;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Throwable $e) {
//            $this->sendBot($e);
        });
    }

    public function sendBot(\Throwable $e): void
    {
        try {
            if (LogLevel::ERROR == Arr::first($this->levels, fn ($level, $type) => $e instanceof $type, LogLevel::ERROR)) {
                $client = Redis::connection('bot')->client();

                $client->publish("bot:error", json_encode([
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]));
            }
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
