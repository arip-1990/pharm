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
            'marked' => $request['marked'] ?? false,
            'recipe' => $request['recipe'] ?? false,
            'status' => $request['status'],
            'category_id' => $request['category'] ?? null,
            'barcodes' => $request['barcodes']
        ]);

        $product->editor()->associate($request->user());
        $product->save();

        if (isset($request['showMain'])) {
            if ($product->statistic) $product->statistic()->update(['show' => $request['showMain']]);
            else $product->statistic()->create(['show' => $request['showMain']]);
        }

        return new JsonResponse();
    }
}
