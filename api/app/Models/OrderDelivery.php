<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ?int $entrance
 * @property ?int $floor
 * @property ?int $apartment
 * @property bool $service_to_door
 * @property float $delivery_price
 *
 * @property Location $location
 */
class OrderDelivery extends Model
{
    public $timestamps = false;

    public static function create(int $entrance = null, int $floor = null, int $apartment = null, bool $serviceToDoor = false, float $deliveryPrice = null): self
    {
        $delivery = new self();
        $delivery->entrance = $entrance;
        $delivery->floor = $floor;
        $delivery->apartment = $apartment;
        $delivery->service_to_door = $serviceToDoor;
        $delivery->delivery_price = $deliveryPrice ?? 0;
        return $delivery;
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
