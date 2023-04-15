<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\{Photo, Product};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Product::select('products.*');

        if ($status = $request->get('offer')) {
            $status === 'on' ? $query->has('offers') : $query->doesntHave('offers');
        }

        if ($sale = $request->get('sale')) {
            $query->where('sale', $sale === 'on');
        }

        if ($photo = $request->get('photo')) {
            switch ($photo) {
                case 'present':
                    $query->has('photos');
                    break;
                case 'missing':
                    $query->doesntHave('photos');
            }
        }

        if ($category = $request->get('category')) {
            $category === 'on' ? $query->whereNotNull('category_id') : $query->whereNull('category_id');
        }

        if ($request->get('searchColumn')) {
            if ($request->get('searchColumn') === 'name') {
                $query->where(function (Builder $builder) use ($request) {
                    $builder->where($request->get('searchColumn'), 'like', '%' . $request->get('searchText') . '%')
                        ->orWhereRaw('to_tsvector(name) @@ plainto_tsquery(?)', [$request->get('searchText')]);
                });
            }
            else {
                $query->where($request->get('searchColumn'), 'like', '%' . $request->get('searchText') . '%');
            }
        }

        if ($request->get('orderField')) {
            if ($request->get('orderField') === 'category') {
                $query->join('categories', 'categories.id', '=', 'products.category_id')
                    ->orderBy('categories.name', $request->get('orderDirection'));
            }
            elseif ($request->get('orderField') === 'barcode') {
                $query->orderByRaw('json_array_length(barcodes) ' . $request->get('orderDirection'));
            }
            else {
                $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
            }
        }

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
