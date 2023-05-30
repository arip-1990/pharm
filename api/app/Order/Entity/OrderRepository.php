<?php

namespace App\Order\Entity;

use App\Exceptions\OrderException;
use App\Models\User;
use App\Store\Entity\{Location, Store};
use App\Order\Entity\Status\{OrderStatus, OrderState, Status};
use App\Order\Event\OrderChangeStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class OrderRepository
{
    public function getById(int $id): Order
    {
        if (!$order = Order::find($id))
            throw new OrderException("Не найден заказ с номером $id!", 2);

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
            throw new OrderException("Статус '{$status->value}' уже присвоен");

        $order->statuses->add(new Status($status, Carbon::now()));
    }

    public function changeState(Order $order, OrderStatus $status = null, OrderState $state = OrderState::STATE_SUCCESS): void
    {
        if ($status) $order->changeStatusState($status, $state);
        else $order->changeState($state);

        OrderChangeStatus::dispatch($order);
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }
}
