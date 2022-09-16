<?php

namespace App\Http\Resources;

use App\Models\VisitStatistic;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var VisitStatistic $this */
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'city' => $this->city,
            'os' => $this->os,
            'browser' => $this->browser,
            'screen' => $this->screen,
            'referrer' => $this->referrer,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->first_name
            ] : null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
