<?php

namespace App\Http\Resources;

use App\Product\Entity\Offer;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Offer $this */
        return [
            'id' => $this->id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'store' => new StoreResource($this->store)
        ];
    }
}
