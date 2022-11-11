<?php

namespace App\Models;

use App\Casts\StatusCollectionCast;
use App\Events\Order\OrderPayFullRefund;
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
 * @property Collection<int, Status> $statuses
 * @property Collection<int, OrderItem> $items
 */
class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id'];
    protected $casts = [
        'statuses' => StatusCollectionCast::class
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
        if ($this->isSent())
            throw new \DomainException('Заказ уже отправлен.');

        $this->addStatus(OrderStatus::STATUS_SEND);
        OrderSend::dispatch($this);
    }

    public function cancel(string $reason = null, OrderStatus $status = OrderStatus::STATUS_CANCELLED): void
    {
        if ($this->isCancelled())
            throw new \DomainException('Заказ уже отменен.');

        $this->cancel_reason = $reason;
        $this->addStatus($status);
    }

    public function assembled(): void
    {
        if ($this->isAssembled())
            throw new \DomainException('Заказ уже собран.');

        $this->addStatus(OrderStatus::STATUS_ASSEMBLED);
    }

    public function refund(): void
    {
        if ($this->isRefund())
            throw new \DomainException('Заказ уже возмещен.');

        $this->addStatus(OrderStatus::STATUS_REFUND);
        OrderPayFullRefund::dispatch($this);
    }

    public function getTotalCost(): float
    {
        return $this->cost;
    }

    //TODO remove archives
    public function getDifferenceOfRefund(): int
    {
        $cost = 0;
        foreach ($this->archives as $archive)
            $cost += $archive->quantity * $archive->price;

        return $cost - $this->cost;
    }

    public function isPay(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_PAID);
    }

    public function isSent(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_SEND);
    }

    public function isCancelled(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_CANCELLED);
    }

    public function isAssembled(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_ASSEMBLED);
    }

    public function isReceived(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_RECEIVED);
    }

    public function isRefund(): bool
    {
        return $this->isStatusSuccess(OrderStatus::STATUS_REFUND);
    }

    public function isStatusSuccess(OrderStatus $status): bool
    {
        return $this->statuses->contains(fn(Status $s) => $s->equal($status) and $s->state === OrderState::STATE_SUCCESS);
    }

    public function inStatus(OrderStatus $status): bool
    {
        return $this->statuses->contains('value', $status);
    }

    public function addStatus(OrderStatus $value, OrderState $state = OrderState::STATE_WAIT): void
    {
        if (!$this->inStatus($value)) {
            $statuses = $this->statuses;
            $status = new Status($value, Carbon::now());
            $status->changeState($state);
            $statuses->add($status);
            $this->statuses = $statuses;
            $this->status = $value;
        }
    }

    public function changeStatusState(OrderStatus $status, OrderState $state): void
    {
        foreach ($this->statuses as $item) {
            if ($item->equal($status)) {
                $item->changeState($state);
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
