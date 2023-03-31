<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStatisticResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $this */
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->first_name . ($this->last_name ? ' ' . $this->last_name : '')
            ],
            'addCountPhotos' => $this->addPhotos->where('updated_at', '>=', Carbon::now()->startOfMonth())->count(),
            'editCountProducts' => $this->editProducts->where('updated_at', '>=', Carbon::now()->startOfMonth())->count(),
            'addTotalCountPhotos' => $this->addPhotos->count(),
            'editTotalCountProducts' => $this->editProducts->count()
        ];
    }
}
