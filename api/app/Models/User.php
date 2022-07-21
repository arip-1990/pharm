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
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property int $role
 * @property string|null $confirm_token
 * @property string|null $reset_token
 * @property string|null $remember_token
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $phone_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property Collection<Order> $orders
 * @property Collection<CartItem> $cartItems
 * @property Limit $priceLimit
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_USER = 10;
    const ROLE_REVIEWER = 20;
    const ROLE_OPERATOR = 30;
    const ROLE_MODERATOR = 40;
    const ROLE_ADMIN = 50;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'email', 'phone', 'password'];
    protected $hidden = ['password', 'remember_token'];

    public static function rolesList(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_REVIEWER,
            self::ROLE_OPERATOR,
            self::ROLE_MODERATOR,
            self::ROLE_ADMIN
        ];
    }

    public static function new(string $name, string $email): self
    {
        return static::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random()),
        ]);
    }

    public static function register(string $name, string $email, string $phone, string $password): self
    {
        return static::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $name,
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

    public function changeRole(int $role): void
    {
        if (!array_key_exists($role, self::rolesList()))
            throw new \InvalidArgumentException('Undefined role "' . $role . '"');

        if ($this->role === $role)
            throw new \DomainException('Role is already assigned.');

        $this->update(['role' => $role]);
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

    public function priceLimit(): HasOne
    {
        return $this->hasOne(Limit::class);
    }
}