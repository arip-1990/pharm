<?php

namespace App\Store\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $inn
 * @property ?string $ogrn
 * @property ?string $license
 * @property ?string $address
 * @property ?string $website
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Store[] $stores
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'inn', 'ogrn', 'license', 'address', 'website'];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }
}
