<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $store_id
 * @property string $product_id
 * @property float $price
 * @property int $quantity
 *
 * @property Store $store
 * @property Product $product
 *
 * @method Builder whereCity(string $city)
 * @method Builder whereInMultiple(array $columns, array $values)
 */
class Offer extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    public function edit(float $price, int $quantity): void
    {
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function isEqualTo(self $offer): bool
    {
        if ($offer instanceof self and $this->product_id === $offer->product_id)
            return true;
        return false;
    }

    public function canBeCheckout(int $quantity): bool
    {
        return $quantity <= $this->quantity;
    }

    public function checkout(int $quantity): void
    {
        if ($quantity > $this->quantity)
            throw new \DomainException('Доступно всего ' . $this->quantity . ' наименований.');

        $this->setQuantity($this->quantity - $quantity);
    }

    private function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function scopeWhereCity(Builder $query, string $city): Builder
    {
        return $query->join('stores', 'offers.store_id', '=', 'stores.id')
        ->where('stores.address', 'like', $city . '%');
    }

    public function scopeWhereInMultiple(Builder $query, array $columns, array $values): Builder
    {
        collect($values)
            ->transform(function ($v) use ($columns) {
                $clause = [];
                foreach ($columns as $index => $column) $clause[] = [$column, '=', $v[$index]];
                return $clause;
            })
            ->each(function($clause, $index) use ($query) {
                $query->where($clause, null, null, $index === 0 ? 'and' : 'or');
            });

        return $query;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
