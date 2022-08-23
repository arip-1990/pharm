<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Collection $coordinate
 * @property ?string $prefix
 * @property ?int $type
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property City $city
 * @property Street $street
 */
class Location extends Model
{
    const TYPE_AVENUE = 0;
    const TYPE_STREET = 1;
    const TYPE_LANE = 2;

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }
}
