<?php

namespace App\Http\Resources;

use App\Models\Photo;
use App\Models\Value;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $photos = $this->photos->map(fn(Photo $photo) => [
            'id' => $photo->id,
            'url' => $photo->getUrl()
        ]);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name
            ] : null,
            'name' => $this->name,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'photos' => $photos->count() ? $photos : [['id' => null, 'url' => url(Photo::DEFAULT_FILE)]],
            'description' => $this->description,
            'marked' => $this->marked,
            'recipe' => $this->recipe,
            'status' => $this->status,
            'attributes' => $this->values->map(fn(Value $value) => [
                'id' => $value->attribute->id,
                'name' => $value->attribute->name,
                'type' => $value->attribute->type,
                'variants' => $value->attribute->variants,
                'value' => $value->value,
            ]),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
