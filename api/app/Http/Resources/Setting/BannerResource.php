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
            'type' => $this->type === Banner::TYPE_MAIN ? 'main' : 'all',
            'sort' => $this->sort
        ];
    }
}
