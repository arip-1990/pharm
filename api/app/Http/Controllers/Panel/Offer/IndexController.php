<?php

namespace App\Http\Controllers\Panel\Offer;

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

        return OfferResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
