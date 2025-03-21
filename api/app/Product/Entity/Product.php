<?php

namespace App\Product\Entity;

use App\Models\User;
use App\Store\Entity\Store;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property int $code
 * @property ?string $description
 * @property Collection<string> $barcodes
 * @property bool $marked
 * @property bool $recipe
 * @property int $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?Category $category
 * @property ?ProductStatistic $statistic
 * @property ?User $editor
 *
 * @property Collection<Photo> $photos
 * @property Collection<Photo> $certificates
 * @property Collection<Offer> $offers
 * @property Collection<Value> $values
 * @property Collection<Discount> $discounts
 */
class Product extends Model
{
    use SoftDeletes, Sluggable;

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_MODERATION = 2;

    public string $abc = '';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'code', 'description', 'marked', 'recipe', 'status'];
    protected $casts = [
        'barcodes' => AsCollection::class
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPrice(Store $store = null): float
    {
        if (!$store) return $this->offers()->first()?->price ?? 0;
        return $this->offers()->where('store_id', $store->id)->first()?->price ?? 0;
    }

    public function activate(): void
    {
        if ($this->isActive())
            throw new \DomainException('Product is already active.');

        $this->status = self::STATUS_ACTIVE;
    }

    public function draft(): void
    {
        if ($this->isDraft())
            throw new \DomainException('Product is already draft.');

        $this->status = self::STATUS_DRAFT;
    }

    public function isActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    protected function scopeActive(Builder $query, string $city = null): Builder
    {
        return $query->whereHas('offers', function (Builder $query) use ($city) {
            $query->whereHas('store', function (Builder $query) use ($city) {
                $query->where('active', true);
                if ($city) $query->where('stores.name', 'like', '%' . $city . '%');
            });
        });
    }

    public function changeCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class)->where('active', true);
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class)->where('type', Photo::TYPE_PICTURE)->orderBy('sort');
    }

    public function certificates(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class)->where('type', Photo::TYPE_CERTIFICATE)->orderBy('sort');
    }

    public function getValue(int $attributeId): ?string
    {
        foreach ($this->values as $value) {
            if ($value->attribute_id === $attributeId)
                return $value->value;
        }
        return null;
    }

    public function values(): HasMany
    {
        return $this->hasMany(Value::class);
    }

    public function getCountByCity(string $city): int
    {
        return $this->offers()->whereCity($city)->count();
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class)->where('quantity', '>', 0);
    }

    public function statistic(): HasOne
    {
        return $this->hasOne(ProductStatistic::class, 'id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
