<?php

namespace App\Http\Controllers\Store;

use App\Http\Resources\StoreResource;
use App\Models\City;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): JsonResource
    {
        $city = City::query()->where('name', $request->cookie('city'))->first() ?? City::query()->find(1);
        $stores = Store::active()->whereIn('location_id', $city->locations->pluck('id'))->paginate(15);
        return StoreResource::customCollection($stores);
    }
}
