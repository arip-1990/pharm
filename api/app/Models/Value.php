<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $value
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Attribute $attribute
 */
class Value extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
