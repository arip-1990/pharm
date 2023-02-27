<?php

namespace App\Order\Entity\Status;

use Carbon\Carbon;

class Status
{
    public OrderStatus $value;
    public OrderState $state;
    public Carbon $created_at;

    public function __construct(OrderStatus $value, Carbon $createdAt, OrderState $state = OrderState::STATE_WAIT)
    {
        $this->value = $value;
        $this->state = $state;
        $this->created_at = $createdAt;
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
