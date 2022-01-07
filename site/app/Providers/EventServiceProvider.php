<?php

namespace App\Providers;

use App\Events\Order\OrderPay;
use App\Events\Order\OrderPayFullRefund;
use App\Events\Order\OrderPayPartlyRefund;
use App\Events\Order\OrderSend;
use App\Listeners\Order\OrderPayFullRefundListener;
use App\Listeners\Order\OrderPayListener;
use App\Listeners\Order\OrderPayPartlyRefundListener;
use App\Listeners\Order\OrderSendListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderSend::class => [
            OrderSendListener::class,
        ],
        OrderPay::class => [
            OrderPayListener::class,
        ],
        OrderPayFullRefund::class => [
            OrderPayFullRefundListener::class,
        ],
        OrderPayPartlyRefund::class => [
            OrderPayPartlyRefundListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
