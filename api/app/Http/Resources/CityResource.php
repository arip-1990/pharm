<?php

namespace App\Http\Resources;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var City $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'prefix' => $this->prefix,
        ];
    }
}
