<?php

namespace App\Http\Resources;

use App\Entities\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
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
                'name' => $this->user->name
            ],
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name
            ],
            'statuses' => $this->statuses->map(fn($status) => [
                'value' => $status['value'],
                'state' => $status['state'] ?? 2,
                'createdAt' => $status['created_at']
            ]),
            'items' => $this->items->map(fn(OrderItem $item) => [
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug
                ],
                'price' => $item->price,
                'quantity' => $item->quantity
            ])
        ];
    }
}
