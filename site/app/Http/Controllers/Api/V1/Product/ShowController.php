<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        $attributes = [];
        foreach($product->values as $value) {
            $attributes[] = [
                'id' => $value->attribute->id,
                'name' => $value->attribute->name,
                'category' => $value->attribute->category,
                'type' => $value->attribute->type,
                'variants' => $value->attribute->variants,
                'required' => $value->attribute->required,
                'value' => $value->value,
            ];
        }

        $photos = [];
        foreach($product->photos as $photo) {
            $photos[] = [
                'id' => $photo->id,
                'sort' => $photo->sort,
                'url' => $photo->getUrl(),
                'status' => $photo->status
            ];
        }
        if (!count($photos)) $photos[] = ['id' => null, 'url' => url(Photo::DEFAULT_FILE)];


        return response()->json([
            'id' => $product->id,
            'slug' => $product->slug,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name
            ] : null,
            'name' => $product->name,
            'code' => $product->code,
            'barcode' => $product->barcode,
            'photos' => $photos,
            'description' => $product->description,
            'marked' => $product->marked,
            'recipe' => $product->recipe,
            'sale' => $product->sale,
            'status' => $product->status,
            'attributes' => $attributes,
            'createdAt' => $product->created_at,
            'updatedAt' => $product->updated_at,
        ]);
    }
}
