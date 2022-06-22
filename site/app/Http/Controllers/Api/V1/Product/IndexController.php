<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Photo;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Product::query()->select('products.*');
        if ($status = $request->get('status')) {
            $query->where('status', $status === 'on');
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
                    $query->doesnthave('photos');
                    break;
                case 'checked':
                    $query->whereHas('photos', function (Builder $builder) {
                        $builder->where('status', Photo::STATUS_CHECKED);
                    });
                    break;
                case 'unchecked':
                    $query->whereHas('photos', function (Builder $builder) {
                        $builder->where('status', Photo::STATUS_NOT_CHECKED);
                    });
            }
        }

        if ($category = $request->get('category')) {
            $category === 'on' ? $query->whereNotNull('category_id') : $query->whereNull('category_id');
        }

        if ($request->get('searchColumn')) {
            $query->where($request->get('searchColumn'), 'like', $request->get('searchText') . '%')
                ->orWhere($request->get('searchColumn'), 'like', '%' . $request->get('searchText') . '%');

            if ($request->get('searchColumn') === 'name')
                $query->orWhereRaw('to_tsvector(name) @@ plainto_tsquery(?)', [$request->get('searchText')]);
        }

        if ($request->get('orderField')) {
            if ($request->get('orderField') === 'category') {
                $query->join('categories', 'categories.id', '=', 'products.category_id')
                    ->orderBy('categories.name', $request->get('orderDirection'));
            }
            else {
                $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
            }
        }

        $query->withCount('photos')->orderByDesc('photos_count');

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
