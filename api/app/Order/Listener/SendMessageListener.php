<?php

namespace App\Order\Listener;

use App\Events\Order\OrderChangeStatus;
use App\Mail\Order\CreateOrder;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendMessageListener implements ShouldQueue
{
    public function handle(OrderChangeStatus $event): void
    {
        $order = $event->order;
        if (!$order->isSent() or ($order->inStatus(OrderStatus::STATUS_MESSAGE) and !$order->isStatusWait(OrderStatus::STATUS_MESSAGE)))
            return;

        $order->addStatus(OrderStatus::STATUS_MESSAGE);
        if (Mail::to($order->user)->send(new CreateOrder($order)))
            $order->changeStatusState(OrderStatus::STATUS_MESSAGE, OrderState::STATE_SUCCESS);
        else
            $order->changeStatusState(OrderStatus::STATUS_MESSAGE, OrderState::STATE_ERROR);
    }
}
