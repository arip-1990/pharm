<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var CartItem $this */
        return [
            'product' => new ProductResource($this->product),
            'quantity' => $this->quantity,
        ];
    }
}
