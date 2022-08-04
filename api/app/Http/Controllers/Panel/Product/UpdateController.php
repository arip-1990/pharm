<?php

namespace App\Http\Controllers\Panel\Product;

use App\Models\Product;
use App\Http\Requests\Api\Product\BaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function handle(Product $product, BaseRequest $request): JsonResponse
    {
        $product->update([
            'name' => $request['name'],
            'status' => $request['status'],
            'category_id' => $request['category'] ?? null,
            'barcode' => $request['barcode'] ?? null
        ]);

        return response()->json();
    }
}
