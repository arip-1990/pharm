<?php

namespace App\Http\Resources;

use App\Helper;
use App\Store\Entity\Store;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public static bool $long = false;

    public function toArray($request): array
    {
        /** @var Store $this */
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'phone' => Helper::formatPhone($this->phone),
            'schedule' => Helper::formatSchedule($this->schedule, static::$long),
            'route' => $this->route,
            'delivery' => $this->delivery,
            'active' => $this->active,
            'location' => new LocationResource($this->location)
        ];
    }

    public static function customCollection(mixed $resource, bool $long = false): JsonResource | AnonymousResourceCollection
    {
        static::$long = $long;
        if ($resource instanceof Store) return new self($resource);

        return parent::collection($resource);
    }
}
