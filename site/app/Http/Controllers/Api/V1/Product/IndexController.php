<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Entities\Product;
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

        if ($request->get('searchColumn'))
            $query->where($request->get('searchColumn'), 'like', $request->get('searchText') . '%');

        $field = $request->get('orderField');
        if ($field) {
            if ($field === 'category')
                $query->join('categories', 'categories.id', '=', 'products.category_id')->orderBy('categories.name', $request->get('orderDirection'));
            else
                $query->orderBy($field, $request->get('orderDirection'));
        }

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
