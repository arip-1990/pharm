<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $type
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<Location> $locations
 */
class City extends Model
{
    const TYPE_CITY = 0;
    const TYPE_TOWNSHIP = 1;
    const TYPE_VILLAGE = 2;
    const TYPE_MICRO_DISTRICT = 3;

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
