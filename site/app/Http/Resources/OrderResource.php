<?php

namespace App\Http\Resources;

use App\Helper;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Order $this */
        return [
            'id' => $this->id,
            'otherId' => $this->id + config('data.orderStartNumber'),
            'cost' => $this->cost,
            'paymentType' => $this->payment_type,
            'deliveryType' => $this->delivery_type,
            'status' => $this->status,
            'note' => $this->note,
            'cancel_reason' => $this->cancel_reason,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => Helper::formatPhone($this->user->phone, true)
            ],
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name
            ],
            'statuses' => $this->statuses->map(fn(Status $status) => [
                'value' => $status->value,
                'state' => $status->state ?? 2,
                'createdAt' => $status->created_at
            ]),
            'items' => OrderItemResource::collection($this->items)
        ];
    }
}
