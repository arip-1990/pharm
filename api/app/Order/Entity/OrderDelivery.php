<?php

namespace App\Order\Entity;

use App\Store\Entity\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property ?int $entrance
 * @property ?int $floor
 * @property ?int $apartment
 * @property bool $service_to_door
 * @property float $price
 *
 * @property Location $location
 */
class OrderDelivery extends Model
{
    public $timestamps = false;

    public static function create(int $entrance = null, int $floor = null, int $apartment = null, bool $serviceToDoor = false, float $price = null): self
    {
        $delivery = new self();
        $delivery->entrance = $entrance;
        $delivery->floor = $floor;
        $delivery->apartment = $apartment;
        $delivery->service_to_door = $serviceToDoor;
        $delivery->price = $price ?? 0;
        return $delivery;
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
