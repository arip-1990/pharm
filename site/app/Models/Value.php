<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $attribute_id
 * @property string $value
 *
 * @property Attribute $attribute
 */
class Value extends Model
{
    public $timestamps = false;

    protected $fillable = ['attribute_id', 'value'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
