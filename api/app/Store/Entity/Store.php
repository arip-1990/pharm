<?php

namespace App\Store\Entity;

use App\Product\Entity\{Offer, Product};
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
 * @property ?string $slug
 * @property ?string $phone
 * @property Collection $schedule
 * @property ?string $route
 * @property bool $delivery
 * @property bool $active
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

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'slug', 'phone'];
    protected $casts = ['schedule' => AsCollection::class];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function activate(): void
    {
        if ($this->active)
            throw new \DomainException('Store is already active.');

        $this->active = true;
    }

    public function inactivate(): void
    {
        if (!$this->active)
            throw new \DomainException('Store is already inactive.');

        $this->active = false;
    }

    public function getPrice(Product $product): float
    {
        return $this->offers()->where('product_id', $product->id)->first()?->price ?? 0;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
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
