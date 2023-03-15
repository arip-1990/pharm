<?php

namespace App\Providers;

use App\Order\Event\{OrderDelivery, OrderChangeStatus, OrderPayPartlyRefund, OrderPayFullRefund, OrderSend};
use App\Order\Listener\{OrderDeliveryListener, SendStatusListener, OrderPayFullRefundListener, OrderPayPartlyRefundListener, OrderSendListener};
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
        OrderChangeStatus::class => [
            SendStatusListener::class
        ]
    ];
}
