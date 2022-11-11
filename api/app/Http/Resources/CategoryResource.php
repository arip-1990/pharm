<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Category $this */
        return [
            'id' => $this->id,
            'parent' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'picture' => $this->getUrl(),
            'children' => CategoryResource::collection($this->children)
        ];
    }
}
