<?php

namespace App\Http\Resources;

use App\Product\Entity\Photo;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Photo $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->getUrl(),
            'sort' => $this->sort,
            'type' => $this->type ? 'Сертификат' : 'Изображение',
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
