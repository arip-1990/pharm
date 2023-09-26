<?php

namespace App\Order\UseCase;

use App\Order\Entity\Delivery;
use App\Order\Entity\Order;
use App\Order\Entity\Payment;
use App\Order\GenerateOrderData;
use Carbon\Carbon;

readonly class GenerateOrder1C implements GenerateOrderData
{
    public function generate(Order $order, Carbon $date = null): string
    {
        $phone = '+' . $order->phone;
        $price_all = $order->getTotalCost();
        $order_number = config('data.orderStartNumber') + $order->id;
        $date = $date ?? Carbon::now();

        $delivery_xml = '';
        if($order->delivery->isType(Delivery::TYPE_PICKUP)) {
            $delivery_xml =
                "<deliveries>
                        <delivery>
                            <type>PICKUP</type>
                            <date_time>{$date->format('Y-m-d H:i:s')}</date_time>
                            <coordinates>
                                <lon>{$order->store->location->coordinate[1]}</lon>
                                <lat>{$order->store->location->coordinate[0]}</lat>
                            </coordinates>
                            <pharmacy>{$order->store_id}</pharmacy>
                        </delivery>
                    </deliveries>";
        }
        elseif($order->delivery->isType(Delivery::TYPE_DELIVERY)) {
            $delivery = $order->orderDelivery;
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
                        <pharmacy>{$order->store_id}</pharmacy>
                    </delivery>
                </deliveries>";
        }

        $isMobile = $order->isMobile() ? 1 : 0;
        $xml = "<orders_request>
            <orders>
                <order>
                    <code>$order_number</code>
                    <date_time>{$date->format('Y-m-d H:i:s')}</date_time>
                    <status_id>O</status_id>
                    <status_date_time>{$date->format('Y-m-d H:i:s')}</status_date_time>
                    <price>$price_all</price>
                    <city>RU-100000</city>
                    <comment>{$order->note}</comment>
                    <is_mobile>{$isMobile}</is_mobile>
                    <customer>
                        <type>PRIVATE</type>
                        <name>{$order->name}</name>
                        <phone>$phone</phone>
                    </customer>" . $delivery_xml;

        if($order->payment->isType(Payment::TYPE_CASH)) {
            $xml .=
                "<payments>
                    <payment>
                        <type>CASH</type>
                        <amount>$price_all</amount>
                    </payment>
                </payments>";
        }
        elseif($order->payment->isType(Payment::TYPE_CARD)) {
            $xml .=
                "<payments>
                    <payment>
                        <type>BANK_TRANSFER</type>
                        <amount>$price_all</amount>
                    </payment>
                </payments>";
        }

        $products = '';
        foreach ($order->items as $item) {
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
}
