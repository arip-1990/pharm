<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property int $views
 * @property int $orders
 * @property int $reviews
 * @property int $cancellations
 * @property float $rating
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property User $user
 */
class ProductStatistic extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'views', 'orders', 'reviews', 'cancellations'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id');
    }
}
