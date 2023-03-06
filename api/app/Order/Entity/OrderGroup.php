<?php

namespace App\Order\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $order_1c_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<Order> $orders
 */
class OrderGroup extends Model
{
    protected $fillable = ['order_1c_id'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
