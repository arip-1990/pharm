<?php

namespace App\Http\Resources;

use App\Models\Delivery;
use App\Models\PickupLocation;
use App\Models\TimeInterval;
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
        if ($data['type'] == 'pickup') {
            $data['locations'] = PickupLocationResource::collection(PickupLocation::where('city', self::$data['addressData']['city'])->get());
        }
        elseif ($data['type'] == 'delivery') {
            $data['dateIntervals'] = [
                'id' => $nowDate->format('y-m-d'),
                "title" => $nowDate->translatedFormat('d M'),
                "subTitle" => "Сегодня",
                "timeIntervals" => TimeIntervalResource::collection(TimeInterval::all())
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
