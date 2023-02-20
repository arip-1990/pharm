<?php

namespace App\Order\UseCase;

use App\Helper;
use App\Order\Entity\Delivery;
use App\Order\Entity\Order;
use App\Order\Entity\Payment;
use Carbon\Carbon;

class GenerateDataService
{
    public function __construct(private readonly Order $order) {}

    public function generateSenData(Carbon $date): string
    {
        $phone = '+' . $this->order->phone;
        $price_all = $this->order->getTotalCost();
        $order_number = config('data.orderStartNumber') + $this->order->id;

        $delivery_xml = '';
        if($this->order->delivery->isType(Delivery::TYPE_PICKUP)) {
            $delivery_xml =
                "<deliveries>
                        <delivery>
                            <type>PICKUP</type>
                            <date_time>{$date->format('Y-m-d H:i:s')}</date_time>
                            <coordinates>
                                <lon>{$this->order->store->location->coordinate[1]}</lon>
                                <lat>{$this->order->store->location->coordinate[0]}</lat>
                            </coordinates>
                            <pharmacy>{$this->order->store_id}</pharmacy>
                        </delivery>
                    </deliveries>";
        }
        elseif($this->order->delivery->isType(Delivery::TYPE_DELIVERY)) {
            $delivery = $this->order->orderDelivery;
            $location = $delivery->location;
            $address = "{$location->city->name}, ул. {$location->street}, д. {$location->house}";
            if ($delivery->apartment) $address .= ", кв. {$delivery->apartment}";
            $delivery_xml = "
                <deliveries>
                    <delivery>
                        <type>COURIER</type>
                        <date_time>{$date->format('Y-m-d H:i:s')}</date_time>
                        <coordinates>
                            <lon>0.773499</lon>
                            <lat>0.679043</lat>
                        </coordinates>
                        <address>{$address}</address>
                        <city>{$location->city->name}</city>
                        <street>{$location->street}</street>
                        <house>{$location->house}</house>";

            $delivery_xml .= $delivery->apartment ? "<apartment>{$delivery->apartment}</apartment>" : '';
            $delivery_xml .= $delivery->floor ? "<floor>{$delivery->floor}</floor>" : '';
            $delivery_xml .= $delivery->entrance ? "<entrance>{$delivery->entrance}</entrance>" : '';
            $delivery_xml .= "<id_service>0</id_service>";

            $delivery_xml .=
                "<courier_phone>+79999999999</courier_phone>
                        <pharmacy>{$this->order->store_id}</pharmacy>
                    </delivery>
                </deliveries>";
        }

        $xml = "<orders_request>
            <orders>
                <order>
                    <code>$order_number</code>
                    <date_time>{$date->format('Y-m-d H:i:s')}</date_time>
                    <status_id>O</status_id>
                    <status_date_time>{$date->format('Y-m-d H:i:s')}</status_date_time>
                    <price>$price_all</price>
                    <city>RU-100000</city>
                    <comment>{$this->order->note}</comment>
                    <customer>
                        <type>PRIVATE</type>
                        <name>{$this->order->name}</name>
                        <phone>$phone</phone>
                    </customer>" . $delivery_xml;

        if($this->order->payment->isType(Payment::TYPE_CASH)) {
            $xml .=
                "<payments>
                    <payment>
                        <type>CASH</type>
                        <amount>$price_all</amount>
                    </payment>
                </payments>";
        }
        elseif($this->order->payment->isType(Payment::TYPE_CARD)) {
            $xml .=
                "<payments>
                    <payment>
                        <type>BANK_TRANSFER</type>
                        <amount>$price_all</amount>
                    </payment>
                </payments>";
        }

        $products = '';
        foreach ($this->order->items as $item) {
            $products .= "
                <product>
                    <code>{$item->product->code}</code>
                    <name>{$item->product->name}</name>
                    <quantity>{$item->quantity}</quantity>
                    <price>{$item->price}</price>
                </product>";
        }

        $xml .= "<products>
                        $products
                    </products>
                </order>
            </orders>
        </orders_request>";

        return $xml;
    }

    public function generateDeliveryData(): string
    {
        $order_number = config('data.orderStartNumber') + $this->order->id;
        $delivery = $this->order->orderDelivery;
        $store = $this->order->store;
        $delivery_address = $delivery->location->city->name . ", " . $delivery->location->street . ", " . $delivery->location->house;
        $coordinates = Helper::getCoordinates($delivery_address);
        $delivery_lon = $coordinates['lon'];
        $delivery_lat = $coordinates['lat'];

        $items = [];
        foreach ($this->order->items as $item) {
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
            'comment' => $this->order->note,
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
                        'email' => $this->order->email,
                        'name' => $this->order->name,
                        'phone' => '+' . $this->order->phone
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
