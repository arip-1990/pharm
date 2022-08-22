<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $house
 * @property ?string $prefix
 * @property ?int $type
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<Location> $locations
 */
class Street extends Model
{
    const TYPE_AVENUE = 0;
    const TYPE_STREET = 1;
    const TYPE_LANE = 2;

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
