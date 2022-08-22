<?php

namespace App\Models\Status;

enum Sber: int
{
    // https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:requests:getorderstatus
    case SBERBANK_ORDER_REGISTERED = 0; // заказ зарегистрирован, но не оплачен
    case SBERBANK_ORDER_PRE_AUTH_DEPOSIT = 1; // предавторизованная сумма удержана (для двухстадийных платежей)
    case SBERBANK_ORDER_AUTH_DEPOSIT = 2; // проведена полная авторизация суммы заказа
    case SBERBANK_ORDER_AUTH_CANCELLED = 3; // авторизация отменена
    case SBERBANK_ORDER_REFUND = 4; // по транзакции была проведена операция возврата
    case SBERBANK_ORDER_AUTH_ISSUER = 5; // инициирована авторизация через сервер контроля доступа банка-эмитента
    case SBERBANK_ORDER_AUTH_REJECTED = 6; // авторизация отклонена
}
