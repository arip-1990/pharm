<?php

namespace App\Http\Controllers\V1\Store;

use App\Http\Resources\StoreResource;
use App\Models\{City, Location, Store};
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): JsonResource
    {
        /** @var City $city */
        $city = City::where('name', $request->cookie('city'))->first() ?? City::find(1);
        $stores = Store::active()->whereIn('location_id', Location::whereCity($city->parent ?? $city)->pluck('id'))
            ->orderBy('name')->paginate(15);

        return StoreResource::customCollection($stores);
    }
}
