<?php

namespace App\Http\Controllers\V1\Store;

use App\Http\Resources\StoreResource;
use App\Models\City;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): JsonResource
    {
        /** @var City $city */
        $city = City::where('name', $request->cookie('city'))->first() ?? City::find(1);
        $locationIds = Location::where('city_id', $city->children()->pluck('id')->add($city->id))->pluck('id');
        $stores = Store::active()->whereIn('location_id', $locationIds)->paginate(15);

        return StoreResource::customCollection($stores);
    }
}
