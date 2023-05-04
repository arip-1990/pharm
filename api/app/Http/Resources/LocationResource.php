<?php

namespace App\Http\Resources;

use App\Store\Entity\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Location $this */
        return [
            'id' => $this->id,
            'city' => $this->city->getName(),
            'address' => $this->getAddress(true),
            'coordinate' => $this->coordinate,
        ];
    }
}
