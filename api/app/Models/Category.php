<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $sort
 * @property ?string $picture
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Category $parent
 * @property Collection<Category> $children
 * @property Collection<Attribute> $attributes
 */
class Category extends Model
{
    use Sluggable;

    protected $fillable = ['id', 'name', 'parent_id'];

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

    public function parentAttributes(): array
    {
        return $this->parent ? $this->parent->allAttributes() : [];
    }

    public function allAttributes(): array
    {
        return array_merge($this->parentAttributes(), $this->attributes()->orderBy('sort')->getModels());
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    public function isParent(string $name): bool
    {
        return $this->ancestors->contains('name', $name);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
