<?php

namespace App\Http\Controllers\V2\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ShowController extends Controller
{
    public function __invoke(Product $product): Response
    {
        return new JsonResponse(new ProductResource($product));
    }
}
