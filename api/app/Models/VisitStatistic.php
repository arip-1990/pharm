<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $ip
 * @property string|null $city
 * @property string $os
 * @property string $browser
 * @property string $screen
 * @property string|null $referrer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property ?User $user
 */
class VisitStatistic extends Model
{
    protected $fillable = ['ip', 'os', 'browser', 'screen', 'city', 'referrer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
