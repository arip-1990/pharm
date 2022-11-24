<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\DeliveryRequest;
use App\Http\Resources\Mobile\DeliveryResource;
use App\Models\City;
use App\Models\Delivery;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class DeliveryController extends Controller
{
    public function handle(DeliveryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $deliveries = [];
        $locations = new Collection();
        foreach (Delivery::all() as $item) {
            if ($item->equalType(Delivery::TYPE_PICKUP) and $city = City::where('name', Helper::trimPrefixCity($data['addressData']['city']))->first()) {
                $productIds = array_column($data['items'], 'privateId');
                $locationIds = Location::whereIn('city_id', $city->children()->pluck('id')->add($city->id))->pluck('id');

                $locations = Store::join('offers', 'stores.id', 'offers.store_id')->select('stores.*')
                    ->where('offers.quantity', '>', 0)->whereIn('offers.product_id', $productIds)
                    ->whereIn('stores.id', config('data.mobileStores'))->whereIn('stores.location_id', $locationIds)
                    ->groupBy('stores.id')->orderByRaw('count(*) desc')->get();

                if ($locations->count()) $deliveries[] = $item;
            }
            else $deliveries[] = $item;
        }

        return new JsonResponse(['deliveries' => DeliveryResource::customCollection($deliveries, $locations)]);
    }
}
