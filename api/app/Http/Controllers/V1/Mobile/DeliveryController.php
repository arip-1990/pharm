<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\DeliveryRequest;
use App\Http\Resources\Mobile\DeliveryResource;
use App\Models\City;
use App\Models\Delivery;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class DeliveryController extends Controller
{
    public function handle(DeliveryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $deliveries = [];
            $locations = new Collection();
            if (!$city = City::where('name', Helper::trimPrefixCity($data['city'] ?? $data['addressData']['settlement']))->first())
                throw new \DomainException('Город неизвестен');

            foreach (Delivery::where('active', true)->get() as $item) {
//                if ($item->id === 3 and !$city->isBookingAvailable())
//                    continue;

                if ($item->isType(Delivery::TYPE_PICKUP)) {
                    $query = Store::select('stores.*')->whereIn('stores.id', config('data.mobileStores')[$city->id])
                        ->groupBy('stores.id')->orderByRaw('count(*) desc');

//                    if ($item->id === 2) {
                    $query->join('offers', 'stores.id', 'offers.store_id')
                        ->where('offers.quantity', '>', 0)->whereIn('offers.product_id', $productIds);
//                    }

                    if ($query->count()) {
                        $locations->put($item->id, $query->get());
                        $deliveries[] = $item;
                    }
                }
                else $deliveries[] = $item;
            }

            return new JsonResponse(['deliveries' => DeliveryResource::customCollection($deliveries, $locations)]);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
