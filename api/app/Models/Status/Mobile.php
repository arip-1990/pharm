<?php

namespace App\Models\Status;

enum Mobile: string
{
    case PLACED = 'placed'; // создан
    case PROCESSING = 'processing'; // в обработке
    case READY_TO_DISPATCH = 'ready_to_dispatch'; // готов к отправке
    case DISPATCHED = 'dispatched'; // отправлен в доставку
    case READY_FOR_PICKUP = 'ready_for_pickup'; // готов к выдаче
    case DELIVERED = 'delivered'; // доставлен
    case CLOSED = 'closed'; // завершен без выкупа
    case CANCELED = 'canceled'; // отменен
    case DONE = 'done'; // выполнен. выкуплен.
}
