<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private ProductRepository $productRepository) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $products = $this->productRepository->getAll($request);
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

        return response()->json($products);
    }
}
