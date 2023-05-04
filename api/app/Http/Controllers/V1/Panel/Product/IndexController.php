<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use App\Product\UseCase\SearchService;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request, SearchService $searchService): ResourceCollection
    {
        $query = Product::select('products.*');

        if ($request->get('searchColumn')) {
            if ($request->get('searchColumn') === 'name') {
                $ids = $searchService->search($request->get('searchText'), $request->get('page', 1) - 1, $request->get('pageSize', 10));
                $query->whereIn('id', $ids)->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"));
            }
            else {
                $query->where($request->get('searchColumn'), '~*', $request->get('searchText'));
            }
        }

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
