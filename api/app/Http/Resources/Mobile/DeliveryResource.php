<?php

namespace App\Http\Resources\Mobile;

use App\Helper;
use App\Models\City;
use App\Models\Delivery;
use App\Models\Location;
use App\Models\Offer;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public static array $data;

    public function toArray($request): array
    {
        /** @var Delivery $this */
        $nowDate = Carbon::now()->locale('ru_RU');
        $data = [
            'id' => (string)$this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' =>$this->type,
            'hasPickupLocations' => false,
            'price' => $this->price,
            'min' => $this->min,
            'max' => $this->max,
            'timeLabel' => 'В день заказа'

        ];
        if ($data['type'] == Delivery::TYPE_PICKUP) {
            if ($city = City::where('name', Helper::trimPrefixCity(self::$data['addressData']['city']))->first()) {
                $productIds = array_column(self::$data['items'], 'privateId');
                $locationIds = Location::whereIn('city_id', $city->children()->pluck('id')->add($city->id))->pluck('id');
                $storeIds = Offer::select('store_id')->whereIn('product_id', $productIds)->groupBy('store_id')->pluck('store_id');

                $data['locations'] = PickupLocationResource::collection(
                    Store::whereIn('id', $storeIds)->whereIn('location_id', $locationIds)->get()
                );
            }
        }
        elseif ($data['type'] == Delivery::TYPE_DELIVERY) {
            $data['dateIntervals'][] = [
                'id' => $nowDate->format('y-m-d'),
                "title" => $nowDate->translatedFormat('d M'),
                "subTitle" => "Сегодня",
                "timeIntervals" => [["id" => '1', "title" => "с 10:00 до 20:00"]]
            ];
        }
        return $data;
    }

    public static function customCollection($resource, array $data): AnonymousResourceCollection
    {
        self::$data = $data;
        return parent::collection($resource);
    }
}
