<?php

namespace App\Http\Resources;

use App\Helper;
use App\Order\Entity\Order;
use App\Order\Entity\Status\Status;
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
            'paymentType' => $this->payment?->type,
            'deliveryType' => $this->delivery?->type,
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
            'platform' => $this->platform,
            'items' => OrderItemResource::collection($this->items),
            'transfer' => ($this->group and $this->delivery_id == 2) ? new OrderResource($this->group->orders()->firstWhere('id', '!=', $this->id)) : null
        ];
    }
}
