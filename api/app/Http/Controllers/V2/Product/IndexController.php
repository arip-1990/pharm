<?php

namespace App\Http\Controllers\V2\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Category;
use App\Product\Entity\Product;
use App\Store\Entity\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function __invoke(Request $request, Category $category = null): Response
    {
        $query = Product::active($request->cookie('city', City::find(1)?->name));
        if ($category) {
            $query->whereIn('category_id', $category->descendants()->pluck('id')->push($category->id));
        }

        $data = $query->paginate($request->get('pageSize', 12));

        return new JsonResponse([
            'data' => ProductResource::collection($data),
            'pagination' => [
                'current' => $data->currentPage(),
                'pageSize' => $data->perPage(),
                'total' => $data->total()
            ]
        ]);
    }
}
