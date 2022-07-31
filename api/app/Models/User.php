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
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $id
 * @property string $first_name
 * @property ?string $last_name
 * @property ?string $middle_name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property int $gender
 * @property ?Carbon $birth_date
 * @property ?string $token
 * @property ?string $session
 * @property ?Carbon $email_verified_at
 * @property ?Carbon $phone_verified_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?Role $role
 * @property Collection<Grant> $grants
 * @property Collection<Order> $orders
 * @property Collection<CartItem> $cartItems
 * @property Limit $priceLimit
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'email', 'phone', 'password'];
    protected $hidden = ['password'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
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
}
