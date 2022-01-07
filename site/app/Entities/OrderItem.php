<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string $product_id
 * @property float $price
 * @property int $quantity
 *
 * @property Product $product
 */
class OrderItem extends Model
{
    public $timestamps = false;

    public static function create(string $productId, float $price, int $quantity): self
    {
        $item = new static();
        $item->product_id = $productId;
        $item->price = $price;
        $item->quantity = $quantity;
        return $item;
    }

    public function getCost(): int
    {
        return $this->price * $this->quantity;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}