<?php

namespace App\Product\Entity;

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
 * @property boolean $show
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Product $product
 */
class ProductStatistic extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'views', 'orders', 'reviews', 'show', 'cancellations'];
    protected $casts = ['rating' => 'float'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id');
    }
}
