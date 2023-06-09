<?php

namespace App\Http\Resources\Setting;

use App\Setting\Entity\Banner;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Banner $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'picture' => [
                'main' => $this->getUrl(),
                'mobile' => $this->getUrl(true)
            ],
            'path' => $this->path,
            'type' => $this->type,
            'sort' => $this->sort
        ];
    }
}
