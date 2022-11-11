<?php

namespace App\Models\Status;

enum OrderStatus: string
{
    case STATUS_ACCEPTED = 'A'; // Принят
    case STATUS_PAID = 'P'; // Оплачен
    case STATUS_SEND = 'S'; // Отправка в 1с
    case STATUS_MESSAGE = 'M'; // Отправка почты
    case STATUS_DELIVERY = 'D'; // Доставка
    case STATUS_REFUND = 'RR'; // Возврат денег

    // 1C
    case STATUS_PROCESSING = 'O'; // В обработке
    case STATUS_ASSEMBLED = 'H'; // Доставлен в аптеку (собран)
    case STATUS_RECEIVED = 'F'; // Заказ получен клиентом
    case STATUS_CANCELLED = 'R'; // Отменен
    case STATUS_DISCREPANCY = 'W'; // Расхождения в заказе
}
