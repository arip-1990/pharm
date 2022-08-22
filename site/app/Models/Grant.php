<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Collection<User> $users
 * @property Collection<Role> $roles
 */
class Grant extends Model
{
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
