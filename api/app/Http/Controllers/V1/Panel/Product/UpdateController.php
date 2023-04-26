<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Requests\Panel\Product\BaseRequest;
use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function handle(Product $product, BaseRequest $request): JsonResponse
    {
        $product->fill([
            'name' => $request['name'],
            'marked' => $request['marked'],
            'recipe' => $request['recipe'],
            'sale' => $request['sale'],
            'status' => $request['status'],
            'category_id' => $request['category'] ?? null,
            'barcode' => $request['barcode'] ?? null
        ]);

        $product->editor()->associate($request->user());
        $product->save();

        return new JsonResponse();
    }
}
