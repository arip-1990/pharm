<?php

namespace App\Http\Resources;

use App\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    private readonly bool $long;

    public function __construct($resource, bool $long = false)
    {
        $this->long = $long;
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'phone' => Helper::formatPhone($this->phone),
            'address' => $this->address,
            'coordinate' => [$this->lat, $this->lon],
            'schedule' => Helper::formatSchedule($this->schedule),
            'route' => $this->route,
            'delivery' => $this->delivery,
            'status' => $this->status,
        ];
    }
}
