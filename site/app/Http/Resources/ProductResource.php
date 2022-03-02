<?php

namespace App\Http\Resources;

use App\Entities\Photo;
use App\Entities\Value;
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
            'status' => $this->status,
            'marked' => $this->marked,
            'attributes' => $this->values->map(fn(Value $value) => [
                'attrubuteName' => $value->attribute->name,
                'attrubuteType' => $value->attribute->type,
                'value' => $value->value,
            ]),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
