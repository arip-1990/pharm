<?php

namespace App\Models\Status;

enum Order: string
{
    case STATUS_CREATED_BY_OPERATOR = 'B'; // Создается оператором
    case STATUS_CALLING = 'C'; // Дозваниваемся
    case STATUS_WAIT_GOODS_ARRIVAL = 'D'; // Ожидает поступления товара (под заказ)
    case STATUS_CONFIRMED = 'E'; // Подтвержден
    case STATUS_RECEIVED_BY_CLIENT = 'F'; // Заказ получен клиентом
    case STATUS_CAUSED_BY_DELIVERY = 'G'; // Вызвана доставка
    case STATUS_ASSEMBLED_PHARMACY = 'H'; // Доставлен в аптеку (собран)
    case STATUS_SENT_IN_1C = 'I'; // Отправка в 1с
    case STATUS_SENT_MAIL = 'J'; // Отправка почты
    case STATUS_PARTLY_REFUND = 'K'; // Частичный возврат
    case STATUS_FULL_REFUND = 'L'; // Полный возврат
    case STATUS_ACCEPTED = 'N'; // Принят
    case STATUS_PROCESSING = 'O'; // В обработке
    case STATUS_PAID = 'P'; // Оплачен
    case STATUS_DISBANDED = 'Q'; // Заказ расформирован (Отменен)
    case STATUS_CANCELLED = 'R'; // Отменен
    case STATUS_SENT = 'S'; // Отправлен
    case STATUS_RETURN_BY_COURIER = 'U'; // Возврат от курьера, полный возврат
}
