<?php

namespace App\Order\Entity;

use App\Models\Location;
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
