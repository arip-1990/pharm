<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $type
 * @property string|null $default
 * @property boolean $required
 * @property array $variants
 *
 * @property ?Category $category
 */
class Attribute extends Model
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_TEXT = 'text';

    public $timestamps = false;
    protected $fillable = ['name', 'type', 'required'];
    protected $casts = [
        'variants' => 'array'
    ];

    public function isString(): bool
    {
        return $this->type === self::TYPE_STRING;
    }

    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
    }

    public function isFloat(): bool
    {
        return $this->type === self::TYPE_FLOAT;
    }

    public function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    public function isSelect(): bool
    {
        return count($this->variants) > 0;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}