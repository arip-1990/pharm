<?php

namespace App\Order\Entity;

use App\Models\{Location, Store, User};
use App\Order\Entity\Status\OrderStatus;
use App\Order\Event\OrderChangeStatus;
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

        $this->changeStatus($order, OrderStatus::STATUS_ACCEPTED);

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

    public function changeStatus(Order $order, OrderStatus $status): void
    {
        if (!$order->inStatus($status))
            throw new \DomainException("Статус '$status->value' уже присвоен");

        $order->addStatus($status);
        OrderChangeStatus::dispatch($this);
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }
}
