<?php

namespace App\Providers;

use App\Events\Order\OrderChangeStatus;
use App\Events\Order\OrderDelivery;
use App\Events\Order\OrderPayFullRefund;
use App\Events\Order\OrderPayPartlyRefund;
use App\Events\Order\OrderSend;
use App\Listeners\Order\OrderDeliveryListener;
use App\Listeners\Order\OrderPayFullRefundListener;
use App\Listeners\Order\OrderPayPartlyRefundListener;
use App\Listeners\Order\OrderSendListener;
use App\Listeners\Order\SendStatusListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
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

    /** Register any events for your application. */
    public function boot(): void
    {
        //
    }

    /** Determine if events and listeners should be automatically discovered. */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
