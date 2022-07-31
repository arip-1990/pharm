<?php

namespace App\Http\Resources;

use App\Helper;
use App\Models\Store;
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
        /** @var Store $this */
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'phone' => Helper::formatPhone($this->phone),
            'schedule' => Helper::formatSchedule($this->schedule),
            'route' => $this->route,
            'delivery' => $this->delivery,
            'status' => $this->status,
        ];
    }
}
