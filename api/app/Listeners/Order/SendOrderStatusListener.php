<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderChangeStatus;
use App\Models\Status\Status;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class SendOrderStatusListener implements ShouldQueue
{
    //https://api1.imshop.io/v1/clients/apteka120/statuses/sync/5f9c5363-917f-4588-83ab-ea4058ccdff6
    public function handle(OrderChangeStatus $event): void
    {
        $order = $event->order;
        $status = $this->getMobileStatus($order->statuses);

        $data = [
            'id' => (string)$order->id,
            'code' => $order->status
        ];
    }

    private function getMobileStatus(Collection $statuses): array
    {
        $data = [];
        $statuses->reverse()->each(function (Status $value) use (&$data) {

        });

        return $data;
    }
}
