<?php

namespace App\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<Order> $orders
 */
class OrderGroup extends Model
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
