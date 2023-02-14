<?php

namespace App\Http\Resources\Mobile;

use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class DeliveryResource extends JsonResource
{
    public static Collection $locations;

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
//            'timeLabel' => $this->id === 3 ? 'Ожидание 2-3 дня' : 'В день заказа'
        ];

        if ($data['type'] === Delivery::TYPE_PICKUP) {
            $data['locations'] = PickupLocationResource::collection(self::$locations->get($this->id));
        }
        elseif ($data['type'] === Delivery::TYPE_DELIVERY) {
            $data['dateIntervals'][] = [
                'id' => $nowDate->format('y-m-d'),
                "title" => $nowDate->translatedFormat('d M'),
                "subTitle" => "Сегодня",
                "timeIntervals" => [["id" => '1', "title" => "с 10:00 до 20:00"]]
            ];
        }

        return $data;
    }

    public static function customCollection($resource, Collection $locations): AnonymousResourceCollection
    {
        self::$locations = $locations;
        return parent::collection($resource);
    }
}
