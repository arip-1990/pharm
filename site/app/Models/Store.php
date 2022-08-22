<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property ?string $phone
 * @property Collection $schedule
 * @property ?string $route
 * @property bool $delivery
 * @property bool $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?Location $location
 * @property Offer[] $offers
 */
class Store extends Model
{
    use SoftDeletes, Sluggable;

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    const STATUS_DELIVERY = true;
    const STATUS_PICKUP = false;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'slug', 'phone'];
    protected $casts = [
        'schedule' => AsCollection::class
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function activate(): void
    {
        if ($this->isActive())
            throw new \DomainException('Store is already active.');

        $this->status = self::STATUS_ACTIVE;
    }

    public function inactivate(): void
    {
        if (!$this->isActive())
            throw new \DomainException('Store is already inactive.');

        $this->status = self::STATUS_INACTIVE;
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function getPrice(Product $product): float
    {
        $offer = $this->offers()->where('product_id', $product->id)->first();
        return $offer->price;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class)->orderBy('price');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
