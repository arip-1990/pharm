<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $ip
 * @property int $limit
 * @property int $requests
 * @property ?Carbon $expired_at
 * @property ?Carbon $last_request
 *
 * @property ?User $user
 */
class Limit extends Model
{
    public $timestamps = false;
    public $incrementing = false;

    protected $keyType = 'string';
    protected $primaryKey = 'ip';
    protected $casts = [
        'expired_at' => 'datetime',
        'last_request' => 'datetime'
    ];

    public static function create(string $ip, int $limit = 15): self
    {
        $price_limit = new static();
        $price_limit->ip = $ip;
        $price_limit->limit = $limit;
        $price_limit->requests = 0;
        $price_limit->expired_at = Carbon::now()->addDay();
        $price_limit->last_request = Carbon::now();
        return $price_limit;
    }

    public function reset(): void
    {
        $this->requests = 0;
        $this->expired_at = Carbon::now()->addDay();
        $this->last_request = Carbon::now();
    }

    public function request(): void
    {
        $this->requests++;
        $this->last_request = Carbon::now();
    }

    public function isExpired(): bool
    {
        return $this->expired_at > Carbon::now() and $this->limit <= $this->requests;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
