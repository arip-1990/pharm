<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property string $city
 * @property string $street
 * @property string $house
 * @property int $entrance
 * @property int $floor
 * @property int $apartment
 * @property bool $service_to_door
 * @property float $delivery_price
 */
class Delivery extends Model
{
    protected $table = 'order_deliveries';
    public $timestamps = false;

    public static function create(string $city, array $address, bool $serviceToDoor = false, float $deliveryPrice = 0): self
    {
        $delivery = new self();
        $delivery->city = $city;
        $delivery->street = $address['street'] ?? null;
        $delivery->house = $address['house'] ?? null;
        $delivery->entrance = $address['entrance'] ?? null;
        $delivery->floor = $address['floor'] ?? null;
        $delivery->apartment = $address['apartment'] ?? null;
        $delivery->service_to_door = $serviceToDoor;
        $delivery->delivery_price = $deliveryPrice;
        return $delivery;
    }

    public function __toString(): string
    {
        return "г. $this->city, $this->street $this->house";
    }
}
