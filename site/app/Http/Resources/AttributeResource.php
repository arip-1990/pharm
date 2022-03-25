<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'default' => $this->default,
            'required' => $this->required,
            'variants' => $this->variants,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name
            ] : null
        ];
    }
}
