<?php

namespace App\Http\Resources;

use App\Order\Entity\OrderItem;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var OrderItem $this */
        return [
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug
            ],
            'price' => $this->price,
            'quantity' => $this->quantity
        ];
    }
}
