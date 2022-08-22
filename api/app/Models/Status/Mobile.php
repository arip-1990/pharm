<?php

namespace App\Models\Status;

enum Mobile: string
{
    case MOBILE_PLACED = 'placed'; // создан
    case MOBILE_PROCESSING = 'processing'; // в обработке
    case MOBILE_READY_TO_DISPATCH = 'ready_to_dispatch'; // готов к отправке
    case MOBILE_DISPATCHED = 'dispatched'; // отправлен в доставку
    case MOBILE_READY_FOR_PICKUP = 'ready_for_pickup'; // готов к выдаче
    case MOBILE_DELIVERED = 'delivered'; // доставлен
    case MOBILE_CLOSED = 'closed'; // завершен без выкупа
    case MOBILE_CANCELED = 'canceled'; // отменен
    case MOBILE_DONE = 'done'; // выполнен. выкуплен.
}
