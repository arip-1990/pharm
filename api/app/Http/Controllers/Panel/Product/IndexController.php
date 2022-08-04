<?php

namespace App\Http\Controllers\Panel\Product;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Product::query()->select('products.*');
        if ($status = $request->get('status'))
            $query->where('status', $status === 'on' ? Product::STATUS_ACTIVE : Product::STATUS_DRAFT);

        if ($photo = $request->get('photo'))
            $photo === 'on' ? $query->has('photos') : $query->doesnthave('photos');

        if ($category = $request->get('category'))
            $category === 'on' ? $query->whereNotNull('category_id') : $query->whereNull('category_id');

        if ($request->get('searchColumn')) {
            if ($request->get('searchColumn') === 'name')
                $query->whereRaw('to_tsvector(name) @@ plainto_tsquery(?)', [$request->get('searchText')]);
            else
                $query->where($request->get('searchColumn'), 'like', $request->get('searchText') . '%');
        }

        if ($request->get('orderField')) {
            if ($request->get('orderField') === 'category') {
                $query->join('categories', 'categories.id', '=', 'products.category_id')
                    ->orderBy('categories.name', $request->get('orderDirection'));
            }
            elseif ($request->get('orderField') === 'photo') {
                $query->withCount('photos')->orderBy('photos_count', $request->get('orderDirection'));
            }
            else {
                $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
            }
        }

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
