<?php

namespace App\Http\Resources;

use App\Models\Photo;
use App\Models\Product;
use App\Models\Value;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Product $this */
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category ? new CategoryResource($this->category) : null,
            'name' => $this->name,
            'code' => $this->code,
            'barcode' => $this->barcode,
            'photos' => $this->photos->map(fn(Photo $photo) => [
                'id' => $photo->id,
                'url' => $photo->getUrl()
            ]),
            'description' => $this->description,
            'status' => $this->status,
            'marked' => $this->marked,
            'recipe' => $this->recipe,
            'attributes' => $this->values->map(fn(Value $value) => [
                'id' => $value->attribute->id,
                'name' => $value->attribute->name,
                'type' => $value->attribute->type,
                'variants' => $value->attribute->variants,
                'value' => $value->value,
            ]),
            'minPrice' => $this->getPrice(),
            'totalOffer' => $this->getCountByCity($request->get('city', config('data.city')[0])),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}