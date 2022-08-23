<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Collection $coordinate
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property City $city
 * @property Street $street
 */
class Location extends Model
{
    protected $fillable = ['coordinate', 'city_id', 'street_id'];

    protected $casts = [
        'coordinate' => AsCollection::class
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }
}
