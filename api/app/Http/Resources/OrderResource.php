<?php

namespace App\Http\Resources;

use App\Helper;
use App\Order\Entity\Order;
use App\Order\Entity\Status\Status;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public static array $orderGroups = [];

    public function toArray($request): array
    {
        /** @var Order $this */
        $transfer = null;
        if ($this->group and !in_array($this->order_group_id, self::$orderGroups)) {
            self::$orderGroups[] = $this->order_group_id;
            $transfer = new OrderResource($this->group->orders()->firstWhere('id', '!=', $this->id));
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
            'customer' => [
                'name' => $this->user?->first_name ?? $this->name,
                'phone' => Helper::formatPhone($this->user?->phone ?? $this->phone),
                'email' => $this->user?->email ?? $this->email
            ],
            'store' => new StoreResource($this->store),
            'statuses' => $this->statuses->map(fn(Status $status) => [
                'value' => $status->value,
                'state' => $status->state ?? 2,
                'createdAt' => $status->created_at->format('d.m.Y')
            ]),
            'platform' => $this->platform,
            'items' => OrderItemResource::collection($this->items),
            'transfer' => $transfer,
            'group_id' => $this->order_group_id,
            'delivery_id' => $this->delivery_id
        ];
    }
}
