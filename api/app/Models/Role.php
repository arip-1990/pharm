<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<User> $users
 * @property Collection<Grant> $grants
 */
class Role extends Model
{
    const ROLE_USER = 'User';
    const ROLE_REVIEWER = 'Reviewer';
    const ROLE_OPERATOR = 'Operator';
    const ROLE_MODERATOR = 'Moderator';
    const ROLE_ADMIN = 'Admin';

    public function isUser(): bool
    {
        return $this->name === self::ROLE_USER;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function grants(): BelongsTo
    {
        return $this->belongsTo(Grant::class);
    }
}
