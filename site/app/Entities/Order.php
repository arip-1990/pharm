<?php

namespace App\Entities;

use App\Events\Order\OrderPay;
use App\Events\Order\OrderPayFullRefund;
use App\Events\Order\OrderPayPartlyRefund;
use App\Events\Order\OrderSend;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
 * @property Collection<Status> $statuses
 * @property OrderItem[] $items
 * @property Archive[] $archives
 *
 * @property Exception $lastException
 * @property Exception[] $exceptions
 */
class Order extends Model
{
    use SoftDeletes;

    protected $casts = [
        'statuses' => AsCollection::class
    ];

    // Тип оплаты
    const PAYMENT_TYPE_CASH = 0;
    const PAYMENT_TYPE_SBER = 1;

    // Способ получения
    const DELIVERY_TYPE_PICKUP = 0;
    const DELIVERY_TYPE_COURIER = 1;

    public static function create(string $userId, string $storeId, int $paymentType, float $cost, bool $deliveryType = false): self
    {
        $item = new static();
        $item->user_id = $userId;
        $item->store_id = $storeId;
        $item->payment_type = $paymentType;
        $item->delivery_type = $deliveryType;
        $item->cost = $cost;
        $item->addStatus(Status::STATUS_ACCEPTED);
        return $item;
    }

    public function setDeliveryInfo(Delivery $delivery): void
    {
        $this->delivery = $delivery;
    }

    public function pay(string $sberId = null): void
    {
        if ($this->isPay())
            throw new \DomainException('Заказ уже оплачен.');

        if ($sberId) {
            $this->sber_id = $sberId;
            $this->addStatus(Status::STATUS_PAID);
            OrderPay::dispatch($this);
        }
        else {
            foreach ($this->statuses as $status) {
                if ($status->equal(Status::STATUS_PAID)) {
                    $status->changeState(Status::STATE_SUCCESS);
                    break;
                }
            }
        }
    }

    public function sent(): void
    {
        if ($this->isSend())
            throw new \DomainException('Заказ уже отправлен.');

        $this->addStatus(Status::STATUS_SENT_IN_1C);
        OrderSend::dispatch($this);
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
        OrderPayPartlyRefund::dispatch($this);
    }

    public function fullRefund(): void
    {
        if ($this->isFullRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(Status::STATUS_FULL_REFUND);
        OrderPayFullRefund::dispatch($this);
    }

    public function getTotalCost(): float
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
        return $this->inStatus(Status::STATUS_PAID) and $this->statuses->contains(function (Status $status) {
            return $status->equal(Status::STATUS_PAID) and $status->state === Status::STATE_SUCCESS;
        });
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
        return $this->statuses->pluck('value')->contains($status);
    }

    public function addStatus(string $value, int $state = Status::STATE_WAIT): void
    {
        $statuses = $this->statuses;
        $status = new Status($value, new \DateTimeImmutable());
        $status->changeState($state);
        $statuses[] = $status;
        $this->statuses = $statuses;
        $this->status = $value;
    }

    public function changeStatusState(int $state): void
    {
        foreach ($this->statuses as $status) {
            if ($status->equal($this->status)) {
                $status->changeState($state);
                break;
            }
        }
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

    public function lastException(): ?self
    {
        return $this->exceptions()->orderBy('id', SORT_DESC)->first();
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(Exception::class, 'initiator_id')
            ->where('initiator', self::class);
    }
}
