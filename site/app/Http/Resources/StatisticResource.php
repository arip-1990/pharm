<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'city' => $this->city,
            'os' => $this->os,
            'browser' => $this->browser,
            'screen' => $this->screen,
            'referrer' => $this->referrer,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
