<?php

namespace App\Models;

class Status
{
    // Сбербанк статусы
// https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:requests:getorderstatus
    const SBERBANK_ORDER_REGISTERED = 0; // заказ зарегистрирован, но не оплачен
    const SBERBANK_ORDER_PRE_AUTH_DEPOSIT = 1; // предавторизованная сумма удержана (для двухстадийных платежей)
    const SBERBANK_ORDER_AUTH_DEPOSIT = 2; // проведена полная авторизация суммы заказа
    const SBERBANK_ORDER_AUTH_CANCELLED = 3; // авторизация отменена
    const SBERBANK_ORDER_REFUND = 4; // по транзакции была проведена операция возврата
    const SBERBANK_ORDER_AUTH_ISSUER = 5; // инициирована авторизация через сервер контроля доступа банка-эмитента
    const SBERBANK_ORDER_AUTH_REJECTED = 6; // авторизация отклонена

// Статусы
    const STATUS_CREATED_BY_OPERATOR = 'B'; // Создается оператором
    const STATUS_CALLING = 'C'; // Дозваниваемся
    const STATUS_WAIT_GOODS_ARRIVAL = 'D'; // Ожидает поступления товара (под заказ)
    const STATUS_CONFIRMED = 'E'; // Подтвержден
    const STATUS_RECEIVED_BY_CLIENT = 'F'; // Заказ получен клиентом
    const STATUS_CAUSED_BY_DELIVERY = 'G'; // Вызвана доставка
    const STATUS_ASSEMBLED_PHARMACY = 'H'; // Доставлен в аптеку (собран)
    const STATUS_SENT_IN_1C = 'I'; // Отправлен в 1с
    const STATUS_SENT_MAIL = 'J'; // Отправлена почта
    const STATUS_PARTLY_REFUND = 'K'; // Частичный возврат
    const STATUS_FULL_REFUND = 'L'; // Полный возврат
    const STATUS_ACCEPTED = 'N'; // Принят
    const STATUS_PROCESSING = 'O'; // В обработке
    const STATUS_PAID = 'P'; // Оплачен
    const STATUS_DISBANDED = 'Q'; // Заказ расформирован (Отменен)
    const STATUS_CANCELLED = 'R'; // Отменен
    const STATUS_SENT = 'S'; // Отправлен
    const STATUS_RETURN_BY_COURIER = 'U'; // Возврат от курьера, полный возврат

    // Состояния
    const STATE_WAIT = 0;
    const STATE_ERROR = 1;
    const STATE_SUCCESS = 2;

    public string $value;
    public int $state;
    public \DateTimeImmutable $created_at;

    public function __construct(string $value, \DateTimeImmutable $created_at)
    {
        $this->value = $value;
        $this->state = self::STATE_WAIT;
        $this->created_at = $created_at;
    }

    public function changeState(int $state): void
    {
        $this->state = $state;
    }

    public function equal(string $value): bool
    {
        return $this->value === $value;
    }
}
