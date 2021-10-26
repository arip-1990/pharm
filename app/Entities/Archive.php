<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $order_id
 * @property string $product_id
 * @property string $store_id
 * @property float $price
 * @property int $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Order $order
 * @property Product $product
 * @property Store $store
 */
class Archive extends Model
{
    public static function create(Order $order, Product $product, float $price, int $quantity): self
    {
        $item = new static();
        $item->order_id = $order->id;
        $item->product_id = $product->id;
        $item->store_id = $order->store_id;
        $item->price = $price;
        $item->quantity = $quantity;
        return $item;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
