<?php

namespace App\Exceptions;

use Carbon\Carbon;
use \Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @property int $id
 * @property string $initiator
 * @property string|null $initiator_id
 * @property string $type
 * @property bool $fixed
 * @property string $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $decided_at
 */
class Model extends BaseModel
{
    protected $table = 'exceptions';
    protected $casts = [
        'decided_at' => 'datetime'
    ];

    public function fix(): void
    {
        if ($this->isFixed())
            throw new \DomainException('Ошибка уже исправлена. Время исправления: ' . $this->decided_at->format('Y-m-d H:i:s'));

        $this->fixed = true;
        $this->decided_at = Carbon::now();
    }

    public function break(): void
    {
        if (!$this->isFixed())
            throw new \DomainException('Ошибка уже актуальна.');

        $this->fixed = false;
        $this->decided_at = null;
    }

    public function isFixed(): bool
    {
        return $this->fixed;
    }
}
