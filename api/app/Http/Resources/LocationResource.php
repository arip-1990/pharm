<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Location $this */
        return [
            'id' => $this->id,
            'city' => $this->city->getName(),
            'address' => $this->getAddress(),
            'coordinate' => $this->coordinate,
        ];
    }
}
