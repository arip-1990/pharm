<?php

namespace App\Http\Controllers\Api\V1\Offer;

use App\Http\Resources\OfferResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Product::query()->whereHas('offers');
        if ($request->get('orderField'))
            $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
        else
            $query->orderByDesc('code');

        return OfferResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
