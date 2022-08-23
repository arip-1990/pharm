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
class Delivery extends Model
{
    protected $table = 'order_deliveries';
    public $timestamps = false;

    public static function create(array $address, bool $serviceToDoor, float $deliveryPrice = null): self
    {
        $delivery = new self();
        $delivery->entrance = $address['entrance'] ?? null;
        $delivery->floor = $address['floor'] ?? null;
        $delivery->apartment = $address['apartment'] ?? null;
        $delivery->service_to_door = $serviceToDoor;
        $delivery->delivery_price = $deliveryPrice ?? 0;
        return $delivery;
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
