<?php

namespace App\Order\UseCase;

use App\Helper;
use App\Order\Entity\Order;
use App\Order\GenerateOrderData;
use Carbon\Carbon;

readonly class GenerateOrderDelivery implements GenerateOrderData
{
    public function generate(Order $order, Carbon $date = null): string
    {
        $order_number = config('data.orderStartNumber') + $order->id;
        $delivery = $order->orderDelivery;
        $store = $order->store;
        $delivery_address = $delivery->location->city->name . ", " . $delivery->location->street . ", " . $delivery->location->house;
        $coordinates = Helper::getCoordinates($delivery_address);
        $delivery_lon = $coordinates['lon'];
        $delivery_lat = $coordinates['lat'];

        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'cost_currency' => 'RUB',
                'cost_value' => (string)$item->price,
                'droppof_point' => 2,
                'extra_id' => 'Ф-' . $item->product->code,
                'pickup_point' => 1,
                'quantity' => $item->quantity,
                'title' => $item->product->name
            ];
        }

        $data = [
            'client_requirements' => ['taxi_class' => 'express'],
            'comment' => $order->note,
            'emergency_contact' => [
                'name' => config('data.yandex.delivery.contact_name'),
                'phone' => '+' . config('data.yandex.delivery.contact_phone')
            ],
            'items' => $items,
            'optional_return' => false,
            'referral_source' => 'Сайт',
            'route_points' => [
                [
                    'address' => [
                        'coordinates' => [$store->location->coordinate[1], $store->location->coordinate[0]],
                        'fullname' => $store->name
                    ],
                    'contact' => [
                        'email' => config('data.yandex.delivery.contact_email'),
                        'name' => config('data.yandex.delivery.contact_name'),
                        'phone' => '+' . $store->phone
                    ],
                    'external_order_id' => (string)$order_number,
                    'pickup_code' => '111111',
                    'point_id' => 1,
                    'skip_confirmation' => false,
                    'type' => 'source',
                    'visit_order' => 1
                ],
                [
                    'address' => [
                        'coordinates' => [$delivery_lon, $delivery_lat],
                        'fullname' => $delivery_address
                    ],
                    'contact' => [
                        'email' => $order->email,
                        'name' => $order->name,
                        'phone' => '+' . $order->phone
                    ],
                    'external_order_id' => (string)$order_number,
                    'pickup_code' => '111111',
                    'point_id' => 2,
                    'skip_confirmation' => false,
                    'type' => 'destination',
                    'visit_order' => 2
                ]
            ],
            'skip_door_to_door' => !$delivery->service_to_door
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
