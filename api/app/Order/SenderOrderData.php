<?php

namespace App\Order;

use App\Order\Entity\Order;

interface SenderOrderData
{
    public function send(Order $order): string;
}