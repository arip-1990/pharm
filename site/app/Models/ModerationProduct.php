<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $status
 * @property string $type
 * @property ?string $comment
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?User $user
 * @property Product $product
 */
class ModerationProduct extends Model
{
    const STATUS_CHECKED = 1;
    const STATUS_NOT_CHECKED = 2;

    protected $fillable = ['status', 'type', 'comment', 'product_id', 'user_id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
