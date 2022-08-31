<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PickupLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var PickupLocation $this */
        
        return [
            'id' => $this->slug_id,
            'title' => $this->title,
            'address' => $this->address,
            'city' => $this->city,
            'time' => 'Каждый день с 10 до 20',
            'subway' => null,
            'mall' => null,
            'lat' => (string)$this->lat,
            'lon' => (string)$this->lon,
            'price' => $this->price,
            'min' => $this->min,
            'timeLabel' => 'Сегодня',
            'notice' => $this->notice,
        ];
    }
}
