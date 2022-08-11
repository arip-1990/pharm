<?php

namespace App\Models;

use Carbon\Carbon;

class Status
{
    // Состояния
    const STATE_WAIT = 0;
    const STATE_ERROR = 1;
    const STATE_SUCCESS = 2;

    public \App\Models\Status\Order $value;
    public int $state;
    public Carbon $created_at;

    public function __construct(\App\Models\Status\Order $value, Carbon $created_at, int $state = self::STATE_WAIT)
    {
        $this->value = $value;
        $this->state = $state;
        $this->created_at = $created_at;
    }

    public function changeState(int $state): void
    {
        $this->state = $state;
    }

    public function equal(\App\Models\Status\Order $value): bool
    {
        return $this->value === $value;
    }
}
