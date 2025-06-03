<?php

namespace App\Http\Resources;

use App\Product\Entity\Offer;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {

        /** @var Offer $this */
        $price = $this->price;
        if (isset($this->discount_rubles) && $this->discount_rubles > 0) {
            $price -= $this->discount_rubles;
        } elseif (isset($this->discount_percent) && $this->discount_percent > 0) {
            $price -= ($price * ($this->discount_percent / 100));
        }
        return [
            'id' => $this->id,
            'price' => round($price, 2),
            'quantity' => $this->quantity,
            'store' => new StoreResource($this->store)
        ];
    }
}
