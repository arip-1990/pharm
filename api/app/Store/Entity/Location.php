<?php

namespace App\Store\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $type
 * @property string $prefix
 * @property string $street
 * @property string $house
 * @property Collection $coordinate
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property City $city
 */
class Location extends Model
{
    use SoftDeletes;

    const TYPE_AVENUE = 0;
    const TYPE_STREET = 1;
    const TYPE_LANE = 2;

    protected $fillable = ['type', 'street', 'house', 'coordinate', 'prefix', 'city_id'];
    protected $casts = [
        'coordinate' => AsCollection::class
    ];

    public function getAddress(bool $full = false): string
    {
        $cityName = '';
        if ($full) {
            $city = $this->city->parent ?? $this->city;
            $cityName = $city->prefix . '. ' . $city->name . ', ';
        }

        $cityName .= $this->city->parent ? $this->city->prefix . '. ' . $this->city->name . ', ' : '';
        $prefix = $this->prefix ? $this->prefix . '. ' : ($this->type === self::TYPE_AVENUE ? 'пр. ' : 'ул. ');

        return $cityName . $prefix . $this->street . ', д. ' . $this->house;
    }

    public function scopeWhereCity(Builder $query, City $city): Builder
    {
        return $query->whereIn('city_id', $city->children()->pluck('id')->add($city->id));
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
