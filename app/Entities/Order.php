<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $store_id
 * @property int $payment_type
 * @property int $delivery_type
 * @property float $cost
 * @property string $status
 * @property string|null $note
 * @property string|null $cancel_reason
 * @property string|null $sber_id
 * @property string|null $yandex_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property Delivery $delivery
 * @property Store $store
 * @property User $user
 *
 * @property Status[] $statuses
 * @property OrderItem[] $items
 * @property Archive[] $archives
 *
 * @property Exception $lastException
 * @property Exception[] $exceptions
 */
class Order extends Model
{
    use SoftDeletes;
    // Тип оплаты
    const PAYMENT_TYPE_CASH = 0;
    const PAYMENT_TYPE_SBERBANK = 1;

    // Способ получения
    const DELIVERY_TYPE_PICKUP = 0;
    const DELIVERY_TYPE_COURIER = 1;

    // Состояния
    const STATE_WAIT = 0;
    const STATE_ERROR = 1;
    const STATE_SUCCESS = 2;

    public function setDeliveryInfo(Delivery $delivery, int $deliveryType = 0): void
    {
        $this->delivery = $delivery;
        $this->delivery_type = $deliveryType;
    }

    public function pay(string $sberId = null): void
    {
        if ($this->isPay())
            throw new \DomainException('Заказ уже оплачен.');

        if ($sberId) {
            $this->sber_id = $sberId;
            $this->addStatus(Status::STATUS_PAID);
        }
    }

    public function sent(): void
    {
        if ($this->isSend())
            throw new \DomainException('Заказ уже отправлен.');

        $this->addStatus(Status::STATUS_SENT_IN_1C);
    }

    public function confirm(): void
    {
        if ($this->isConfirmed())
            throw new \DomainException('Заказ уже подтвержден.');

        $this->addStatus(Status::STATUS_CONFIRMED);
    }

    public function cancel(string $reason = null, string $status = Status::STATUS_CANCELLED): void
    {
        if ($this->status === $status)
            throw new \DomainException('Заказ уже отменен.');

        $this->cancel_reason = $reason;
        $this->addStatus($status);
    }

    public function assembled(): void
    {
        if ($this->isAssembled())
            throw new \DomainException('Заказ уже собран.');

        $this->addStatus(Status::STATUS_ASSEMBLED_PHARMACY);
    }

    public function partlyRefund(): void
    {
        if ($this->isPartlyRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(Status::STATUS_PARTLY_REFUND);
    }

    public function fullRefund(): void
    {
        if ($this->isFullRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(Status::STATUS_FULL_REFUND);
    }

    public function getTotalCost(): int
    {
        if ($this->delivery_type === self::DELIVERY_TYPE_COURIER)
            return $this->cost + Delivery::DELIVERY_PRICE;
        return $this->cost;
    }

    public function getDifferenceOfRefund(): int
    {
        $cost = 0;
        foreach ($this->archives as $archive)
            $cost += $archive->quantity * $archive->price;

        return $cost - $this->cost;
    }

    public function isPay(): bool
    {
        return $this->inStatus(Status::STATUS_PAID);
    }

    public function isSend(): bool
    {
        return $this->inStatus(Status::STATUS_SENT_IN_1C);
    }

    public function isAccepted(): bool
    {
        return $this->inStatus(Status::STATUS_ACCEPTED);
    }

    public function isConfirmed(): bool
    {
        return $this->inStatus(Status::STATUS_CONFIRMED);
    }

    public function isCancelled(): bool
    {
        return $this->inStatus(Status::STATUS_CANCELLED);
    }

    public function isAssembled(): bool
    {
        return $this->inStatus(Status::STATUS_ASSEMBLED_PHARMACY);
    }

    public function isReceived(): bool
    {
        return $this->inStatus(Status::STATUS_RECEIVED_BY_CLIENT);
    }

    public function isDisbanded(): bool
    {
        return $this->inStatus(Status::STATUS_DISBANDED);
    }

    public function isReturned(): bool
    {
        return $this->inStatus(Status::STATUS_RETURN_BY_COURIER);
    }

    public function isPartlyRefund(): bool
    {
        return $this->inStatus(Status::STATUS_PARTLY_REFUND);
    }

    public function isFullRefund(): bool
    {
        return $this->inStatus(Status::STATUS_FULL_REFUND);
    }

    public function inStatus(string $status): bool
    {
        return in_array($status, array_column($this->statuses, 'value'));
    }

    public function addStatus(string $value, int $state = 0): void
    {
        $statuses = $this->statuses;
        $statuses[] = new Status($value, $state, new \DateTimeImmutable());
        $this->statuses = $statuses;
        $this->status = $value;
    }

    ##########################

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastException(): self
    {
        return $this->exceptions()->orderBy('id', SORT_DESC)->first();
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(Exception::class, 'initiator_id')
            ->where('initiator', self::class);
    }
}
