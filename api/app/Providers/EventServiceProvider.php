<?php

namespace App\Providers;

use App\Order\Event\{OrderSend, OrderDelivery, OrderPayPartlyRefund, OrderPayFullRefund};
use App\Order\Listener\OrderDeliveryListener;
use App\Order\Listener\OrderPayFullRefundListener;
use App\Order\Listener\OrderPayPartlyRefundListener;
use App\Order\Listener\OrderSendListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderSend::class => [
            OrderSendListener::class,
        ],
        OrderDelivery::class => [
            OrderDeliveryListener::class,
        ],
        OrderPayPartlyRefund::class => [
            OrderPayPartlyRefundListener::class,
        ],
        OrderPayFullRefund::class => [
            OrderPayFullRefundListener::class,
        ],
//        OrderChangeStatus::class => [
//            SendStatusListener::class
//        ]
    ];
}
