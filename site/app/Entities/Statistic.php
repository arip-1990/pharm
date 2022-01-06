<?php

namespace App\Entities;

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
 * @property User $user
 */
class Statistic extends Model
{
    public static function create(string $ip, string $os, string $browser, string $screen = '', string $city = null, string $referrer = null): self
    {
        $statistic = new static();
        $statistic->ip = $ip;
        $statistic->city = $city;
        $statistic->os = $os;
        $statistic->browser = $browser;
        $statistic->screen = $screen;
        $statistic->referrer = $referrer;
        return $statistic;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
