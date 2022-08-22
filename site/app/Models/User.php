<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;

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
 * @property Limit $priceLimit
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'first_name', 'last_name', 'middle_name', 'email', 'phone', 'gender', 'birth_date', 'token', 'password'];
    protected $hidden = ['password'];
    protected $casts = [
        'birth_date' => 'datetime',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public static function new(string $name, string $email): self
    {
        return static::create([
            'id' => Uuid::uuid4()->toString(),
            'first_name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random()),
        ]);
    }

    public static function register(string $name, string $email, string $phone, string $password): self
    {
        return static::create([
            'id' => Uuid::uuid4()->toString(),
            'first_name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
        ]);
    }

    public function verify(): void
    {
        if (!$this->isWait())
            throw new \DomainException('User is already verified.');

        $this->update(['confirm_token' => null]);
    }

    public function validatePassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderByDesc('id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function moderationProducts(): HasMany
    {
        return $this->hasMany(ModerationProduct::class);
    }

    public function priceLimit(): HasOne
    {
        return $this->hasOne(Limit::class);
    }
}
