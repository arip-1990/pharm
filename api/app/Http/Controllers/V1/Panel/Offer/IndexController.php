<?php

namespace App\Http\Controllers\V1\Panel\Offer;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Product::query()->whereHas('offers');
        if ($request->get('searchColumn')) {
            if ($request->get('searchColumn') === 'name')
                $query->whereRaw('to_tsvector(name) @@ plainto_tsquery(?)', [$request->get('searchText')]);
            else
                $query->where($request->get('searchColumn'), 'like', $request->get('searchText') . '%');
        }

        if ($request->get('orderField'))
            $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
        else
            $query->orderByDesc('code');

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
