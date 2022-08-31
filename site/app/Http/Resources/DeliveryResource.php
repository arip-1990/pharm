<?php

namespace App\Http\Resources;

use App\Models\PickupLocation;
use App\Models\TimeInterval;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public static $data;

    public function toArray($request)
    {
        /** @var DeliveryType $this */

        $nowDate = Carbon::now()->locale('ru_RU');
        $data = [
            'id' => $this->slug_id,
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
        if ($data['type'] == 'delivery') {
            $data['dateIntervals'] = [
                'id' => $nowDate->format('y-m-d'),
                "title" => $nowDate->translatedFormat('d M'),
                "subTitle" => "Сегодня",
                "timeIntervals" => TimeIntervalResource::collection(TimeInterval::all())
            ];
        }
        return $data;
    }

    public static function customCollection($resource, $data): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        self::$data = $data;
        return parent::collection($resource);
    }
}
