<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Models\Product;
use App\Http\Requests\Panel\Product\BaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function handle(Product $product, BaseRequest $request): JsonResponse
    {
        $product->update([
            'name' => $request['name'],
            'marked' => $request['marked'],
            'recipe' => $request['recipe'],
            'sale' => $request['sale'],
            'status' => $request['status'],
            'category_id' => $request['category'] ?? null,
            'barcode' => $request['barcode'] ?? null
        ]);

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
