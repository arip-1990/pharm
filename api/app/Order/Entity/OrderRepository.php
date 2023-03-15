<?php

namespace App\Order\Entity;

use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use App\Order\Entity\Status\OrderState;
use App\Order\Entity\Status\OrderStatus;
use App\Order\Entity\Status\Status;
use App\Order\Event\OrderChangeStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class OrderRepository
{
    public function getById(int $id): Order
    {
        if (!$order = Order::find($id))
            throw new \DomainException("Не найден заказ с номером $id!", 2);

        return $order;
    }

    public function getByUser(User $user, int $total = 12): Paginator
    {
        return Order::where('user_id', $user->id)->paginate($total);
    }

    public function getAll(int $total = 12): Paginator
    {
        return Order::paginate($total);
    }

    public function create(Store $store, Payment $payment, Delivery $delivery, string $note = null): Order
    {
        $order = new Order([
            'store_id' => $store->id,
            'payment_id' => $payment->id,
            'delivery_ id' => $delivery->id,
            'note' => $note
        ]);

        $this->addStatus($order, OrderStatus::STATUS_ACCEPTED);

        return $order;
    }

    public function createDelivery(Location $location, int $entrance = null, int $floor = null, int $apartment = null, bool $serviceToDoor = false, float $price = null): OrderDelivery
    {
        $delivery = new OrderDelivery([
            'entrance' => $entrance,
            'floor' => $floor,
            'apartment' => $apartment,
            'service_to_door' => $serviceToDoor,
            'price' => $price ?? 0,
        ]);

        $delivery->location()->associate($location);

        return $delivery;
    }

    public function addStatus(Order $order, OrderStatus $status): void
    {
        if ($order->inStatus($status))
            throw new \DomainException("Статус '$status->value' уже присвоен");

        $order->statuses->add(new Status($status, Carbon::now()));
    }

    public function changeState(Order $order, OrderState $state = OrderState::STATE_SUCCESS): void
    {
        $order->changeState($state);
        OrderChangeStatus::dispatch($order);
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }
}
