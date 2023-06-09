<?php

namespace App\Http\Controllers\V2\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use App\Store\Entity\City;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Carbon::setLocale('ru');
        $data = Product::active($request->cookie('city', City::find(1)?->name))
            ->whereHas('discounts', function (Builder $query) {
                $query->where('active', true)->where('expired_at', '>', Carbon::now());
            })
            ->paginate($request->get('pageSize', 20));

        return new JsonResponse([
            'title' => 'Период действия акции с 1 по ' . Carbon::now()->endOfMonth()->translatedFormat('d F Yг.'),
            'data' => ProductResource::collection($data),
            'pagination' => [
                'current' => $data->currentPage(),
                'pageSize' => $data->perPage(),
                'total' => $data->total()
            ]
        ]);
    }
}
