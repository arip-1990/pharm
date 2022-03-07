<?php

namespace App\Entities;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property int $code
 * @property string|null $barcode
 * @property string|null $description
 * @property bool $status
 * @property bool $marked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property Category|null $category
 * @property Collection<Photo> $photos
 * @property Collection<Photo> $certificates
 * @property Collection<Offer> $offers
 * @property Collection<Value> $values
 * @property ProductStatistic|null $statistic
 */
class Product extends Model
{
    use SoftDeletes, Sluggable;

    const STATUS_DRAFT = false;
    const STATUS_ACTIVE = true;

    public string $abc = '';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'code', 'category_id', 'barcode', 'description', 'status', 'marked'];

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

    public function getPrice(Store $store = null): float
    {
        if (!$store) return $this->offers()->first()->price ?? 0;
        $offer = $this->offers()->where('store_id', $store->id)->first();
        return $offer->price ?? 0;
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

    public function isPrescription(): bool
    {
        foreach ($this->values as $value) {
            if ($value->attribute_id === 4 and ($value->value === 'По рецепту' or $value->value === 'По назначению врача'))
                return true;
        }
        return false;
    }

    protected function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)->where('name', 'not like', '*%');
    }

    public function changeCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addPhoto(): void
    {
        $this->photos()->create(['type' => Photo::TYPE_PICTURE]);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->where('type', Photo::TYPE_PICTURE);
    }

    public function addCertificate(): void
    {
        $this->certificates()->create(['type' => Photo::TYPE_CERTIFICATE]);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Photo::class)->where('type', Photo::TYPE_CERTIFICATE);
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

    public function getCount(): int
    {
        /** @var Offer $offer */
        $offer = $this->offers()->orderBy('quantity', 'desc')->first();
        return $offer ? $offer->quantity : 0;
    }

    public function getCountByCity(string $city): int
    {
        return $this->offers()->whereCity($city)->count();
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class)->where('quantity', '>', 0)->orderBy('price');
    }

    public function statistic(): HasOne
    {
        return $this->hasOne(ProductStatistic::class, 'id');
    }
}
