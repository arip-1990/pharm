<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float $price
 * @property int $quantity
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Store $store
 * @property Product $product
 *
 * @method Builder whereCity(string $city)
 * @method Builder whereInMultiple(array $columns, array $values)
 */
class Offer extends Model
{
    public function edit(float $price, int $quantity): void
    {
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function isEqualTo(self $offer): bool
    {
        if ($this->product_id === $offer->product_id)
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
        return $query->whereHas('store', function (Builder $query) use ($city) {
            $query->where('status', Store::STATUS_ACTIVE)
                ->where('name', 'like', '%' . $city . '%');
        });
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
                $query->where($clause, boolean: $index === 0 ? 'and' : 'or');
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
