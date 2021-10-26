<?php

namespace App\Entities;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $phone
 * @property string|null $address
 * @property float|null $lon
 * @property float|null $lat
 * @property array $schedule
 * @property string|null $route
 * @property bool $status
 * @property bool $delivery
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
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
    protected $fillable = ['name', 'slug', 'phone', 'address'];
    protected $casts = [
        'schedule' => 'array'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function setCoordinate(float $lon, float $lat): void
    {
        $this->lon = $lon;
        $this->lat = $lat;
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
}
