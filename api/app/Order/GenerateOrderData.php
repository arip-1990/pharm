<?php

namespace App\Order;

use App\Order\Entity\Order;
use Carbon\Carbon;

interface GenerateOrderData
{
    public function generate(Order $order, Carbon $date = null): string;
}
