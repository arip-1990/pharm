<?php

namespace App\Http\Resources;

use App\Helper;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Status\Status;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Order $this */
        if ($this->user) {
            $customer = [
                'name' => $this->user->first_name,
                'phone' => Helper::formatPhone($this->user->phone, true),
                'email' => $this->user->email
            ];
        }
        else {
            $customer = [
                'name' => $this->name,
                'phone' => Helper::formatPhone($this->phone, true),
                'email' => $this->email
            ];
        }

        return [
            'id' => $this->id,
            'otherId' => $this->id + config('data.orderStartNumber'),
            'cost' => $this->cost,
            'paymentType' => $this->payment->type,
            'deliveryType' => $this->delivery->type,
            'note' => $this->note,
            'cancel_reason' => $this->cancel_reason,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'customer' => $customer,
            'store' => new StoreResource($this->store),
            'statuses' => $this->statuses->map(fn(Status $status) => [
                'value' => $status->value,
                'state' => $status->state ?? 2,
                'createdAt' => $status->created_at->format('d.m.Y')
            ]),
            'items' => $this->items->map(fn(OrderItem $item) => [
                'product' => new ProductResource($item->product),
                'price' => $item->price,
                'quantity' => $item->quantity
            ])
        ];
    }
}
