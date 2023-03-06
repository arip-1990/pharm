<?php

namespace App\Order\Entity;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float $price
 * @property int $quantity
 * @property string $product_id
 *
 * @property Product $product
 */
class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['price', 'quantity'];

    public static function create(string $productId, float $price, int $quantity): self
    {
        $item = new static();
        $item->product_id = $productId;
        $item->price = $price;
        $item->quantity = $quantity;
        return $item;
    }

    public function getCost(): float
    {
        return $this->price * $this->quantity;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
