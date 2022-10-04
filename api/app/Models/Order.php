<?php

namespace App\Models;

use App\Casts\StatusCollection;
use App\Events\Order\OrderPayFullRefund;
use App\Events\Order\OrderPayPartlyRefund;
use App\Events\Order\OrderSend;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use App\Models\Status\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ?string $name
 * @property ?string $phone
 * @property ?string $email
 * @property float $cost
 * @property OrderStatus $status
 * @property ?string $note
 * @property ?string $cancel_reason
 * @property ?string $sber_id
 * @property ?string $yandex_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?User $user
 * @property ?Store $store
 * @property ?Payment $payment
 * @property ?Delivery $delivery
 * @property OrderDelivery $orderDelivery
 *
 * @property Collection<Status> $statuses
 * @property Collection<OrderItem> $items
 */
class Order extends Model
{
    use SoftDeletes;

    protected $casts = [
        'statuses' => StatusCollection::class
    ];

    public static function create(Store $store, Payment $payment, float $cost, Delivery $delivery, string $note = null): self
    {
        $item = new static();
        $item->store_id = $store->id;
        $item->payment_id = $payment->id;
        $item->delivery_id = $delivery->id;
        $item->cost = $cost;
        $item->note = $note;
        $item->addStatus(OrderStatus::STATUS_ACCEPTED, OrderState::STATE_SUCCESS);
        return $item;
    }

    public function setUserInfo(string $name, string $phone, string $email = null): void
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
    }

    public function pay(string $sberId): void
    {
        if ($this->isPay())
            throw new \DomainException('Заказ уже оплачен.');

        $this->sber_id = $sberId;
        $this->addStatus(OrderStatus::STATUS_PAID);
    }

    public function sent(): void
    {
        if ($this->isSend())
            throw new \DomainException('Заказ уже отправлен.');

        $this->addStatus(OrderStatus::STATUS_SENT_IN_1C);
        OrderSend::dispatch($this);
    }

    public function confirm(): void
    {
        if ($this->isConfirmed())
            throw new \DomainException('Заказ уже подтвержден.');

        $this->addStatus(OrderStatus::STATUS_CONFIRMED);
    }

    public function cancel(string $reason = null, OrderStatus $status = OrderStatus::STATUS_CANCELLED): void
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

        $this->addStatus(OrderStatus::STATUS_ASSEMBLED_PHARMACY);
    }

    public function partlyRefund(): void
    {
        if ($this->isPartlyRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(OrderStatus::STATUS_PARTLY_REFUND);
        OrderPayPartlyRefund::dispatch($this);
    }

    public function fullRefund(): void
    {
        if ($this->isFullRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(OrderStatus::STATUS_FULL_REFUND);
        OrderPayFullRefund::dispatch($this);
    }

    public function getTotalCost(): float
    {
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
        return $this->statuses->contains(function (Status $status) {
            return $status->equal(OrderStatus::STATUS_PAID) and $status->state === OrderState::STATE_SUCCESS;
        });
    }

    public function isSend(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_SENT_IN_1C);
    }

    public function isAccepted(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_ACCEPTED);
    }

    public function isConfirmed(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_CONFIRMED);
    }

    public function isCancelled(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_CANCELLED);
    }

    public function isAssembled(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_ASSEMBLED_PHARMACY);
    }

    public function isReceived(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_RECEIVED_BY_CLIENT);
    }

    public function isDisbanded(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_DISBANDED);
    }

    public function isReturned(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_RETURN_BY_COURIER);
    }

    public function isPartlyRefund(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_PARTLY_REFUND);
    }

    public function isFullRefund(): bool
    {
        return $this->inStatus(OrderStatus::STATUS_FULL_REFUND);
    }

    public function inStatus(OrderStatus $status): bool
    {
        return $this->statuses->pluck('value')->contains($status);
    }

    public function addStatus(OrderStatus $value, OrderState $state = OrderState::STATE_WAIT): void
    {
        $statuses = $this->statuses;
        $status = new Status($value, Carbon::now());
        $status->changeState($state);
        $statuses->add($status);
        $this->statuses = $statuses;
        $this->status = $value;
    }

    public function changeStatusState(OrderState $state): void
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

    public function orderDelivery(): HasOne
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
