<?php

namespace App\Providers;

use App\Order\SenderOrderData;
use App\Order\UseCase\SendDataTo1C;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        $this->app->bind(SenderOrderData::class, SendDataTo1C::class);
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        //
    }
}
