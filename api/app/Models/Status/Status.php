<?php

namespace App\Models\Status;

use Carbon\Carbon;

class Status
{
    public OrderStatus $value;
    public OrderState $state;
    public Carbon $created_at;

    public function __construct(OrderStatus $value, Carbon $created_at, OrderState $state = OrderState::STATE_WAIT)
    {
        $this->value = $value;
        $this->state = $state;
        $this->created_at = $created_at;
    }

    public function changeState(OrderState $state): void
    {
        $this->state = $state;
    }

    public function equal(OrderStatus $value): bool
    {
        return $this->value === $value;
    }
}
