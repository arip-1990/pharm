<?php

namespace App\Http\Controllers\Panel\Product;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\Value;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateAttributesController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        $values = [];
        foreach ($request->all() as $key => $value) {
            try {
                $attribute = Attribute::query()->where('name', $key)->firstOrFail();
                $values[] = new Value(['attribute_id' => $attribute->id, 'value' => $value]);
            }
            catch (ModelNotFoundException $e) {}
        }

        $product->values()->delete();
        $product->values()->saveMany($values);

        return response()->json();
    }
}
