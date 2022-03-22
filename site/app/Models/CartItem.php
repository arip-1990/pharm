<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $quantity
 * @property string $product_id
 *
 * @property User $user
 * @property Product $product
 */
class CartItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['quantity', 'product_id', 'user_id'];

    public function plus(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function changeQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getAmount(Store $store = null): float
    {
        return round($this->quantity * $this->product->getPrice($store), 2);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
