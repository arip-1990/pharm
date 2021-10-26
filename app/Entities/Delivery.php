<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $order_id
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $entrance
 * @property int $floor
 * @property string $apartment
 * @property bool $service_to_door
 * @property float $delivery_price
 */
class Delivery extends Model
{
    const DELIVERY_PRICE = 150;

    public static function create(string $city, array $address, bool $serviceToDoor, float $deliveryPrice = null): self
    {
        $delivery = new self();
        $delivery->city = $city;
        $delivery->street = $address['street'] ?? null;
        $delivery->house = $address['house'] ?? null;
        $delivery->entrance = $address['entrance'] ?? null;
        $delivery->floor = $address['floor'] ?? null;
        $delivery->apartment = $address['apartment'] ?? null;
        $delivery->service_to_door = $serviceToDoor;
        $delivery->delivery_price = $deliveryPrice ?? self::DELIVERY_PRICE;
        return $delivery;
    }

    public function __toString(): string
    {
        return "г. $this->city, $this->street $this->house";
    }
}
