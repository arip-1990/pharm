<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $id
 * @property ?string $username
 * @property string $first_name
 * @property ?string $last_name
 * @property ?string $middle_name
 * @property string $phone
 * @property ?string $email
 * @property string $password
 * @property int $gender
 * @property ?Carbon $birth_date
 * @property ?Carbon $phone_verified_at
 * @property ?Carbon $email_verified_at
 * @property ?string $token
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?Role $role
 * @property Collection<Grant> $grants
 * @property Collection<Order> $orders
 * @property Collection<VisitStatistic> $visits
 * @property Collection<ModerationProduct> $moderationProducts
 * @property Limit $priceLimit
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'email',
        'gender',
        'birth_date',
        'password',
        'phone_verified_at',
        'email_verified_at',
        'token'
    ];
    protected $hidden = ['password'];
    protected $casts = [
        'birth_date' => 'datetime',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function getFullName(): string
    {
        $name = $this->last_name ? ($this->last_name . ' ') : '';
        $name .= $this->first_name;
        $name .= $this->middle_name ? (' ' . $this->middle_name) : '';

        return $name;
    }

    public function getGenderLabel(): string
    {
        return match ($this->gender) {
            1 => 'male',
            2 => 'female',
            default => '',
        };
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(VisitStatistic::class);
    }

    public function moderationProducts(): HasMany
    {
        return $this->hasMany(ModerationProduct::class);
    }

    public function priceLimit(): HasOne
    {
        return $this->hasOne(Limit::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function grants(): BelongsTo
    {
        return $this->belongsTo(Grant::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
