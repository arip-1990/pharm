<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property float $percent
 * @property boolean $active
 * @property ?Carbon $started_at
 * @property ?Carbon $expired_at
 *
 * @property Collection<Product> $products
 */
class Discount extends Model
{
    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
