<?php

namespace App\Http\Resources;

use App\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'items' => $this->offers->map(fn(Offer $item) => [
                'store' => [
                    'id' => $item->store->id,
                    'name' => $item->store->name,
                    'slug' => $item->store->slug
                ],
                'price' => $item->price,
                'quantity' => $item->quantity
            ])
        ];
    }
}
