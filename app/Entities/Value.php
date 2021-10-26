<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $attribute_id
 * @property string $value
 *
 * @property Attribute $attribute
 */
class Value extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = null;
    protected $fillable = ['attribute_id', 'value'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
