<?php

namespace App\Http\Resources\Mobile;

use App\Models\Store;
use Illuminate\Http\Resources\Json\JsonResource;

class PickupLocationResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Store $this */
        return [
            'id' => $this->id,
            'title' => $this->name,
            'address' => $this->location?->getAddress(),
            'city' => $this->location?->city->getName(),
            'time' => 'Каждый день с 10 до 20',
            'subway' => null,
            'mall' => null,
            'lat' => (string)$this->location?->coordinate[0],
            'lon' => (string)$this->location?->coordinate[1],
            'price' => 0,
            'min' => 0,
            'timeLabel' => 'Сегодня',
            'notice' => null,
        ];
    }
}
