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
            'city' => $this->city->name,
            'street' => $this->street->prefix . '. ' . $this->street->name,
            'house' => 'д.' . $this->street->house,
            'coordinate' => $this->coordinate,
        ];
    }
}
