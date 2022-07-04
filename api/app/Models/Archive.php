<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int $code
 * @property string $name
 * @property float $price
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property ?Store $store
 */
class Archive extends Model
{
    protected $fillable = ['order_id', 'code', 'name', 'price', 'quantity'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
