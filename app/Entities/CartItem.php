<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $quantity
 * @property string $product_id
 *
 * @property User $user
 * @property Product $product
 */
class CartItem extends Model
{
    protected $fillable = ['product_id', 'quantity'];
    public $incrementing = false;

    public static function create(string $productId, int $quantity): self
    {
        $item = new static();
        $item->product_id = $productId;
        $item->quantity = $quantity;
        return $item;
    }

    public function plus(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function changeQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
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
