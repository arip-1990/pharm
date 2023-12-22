<?php

namespace App\Console\Commands\Order;

use App\Models\Status\Platform;
use App\Order\SenderOrderData;
use Illuminate\Support\Collection;
use App\Order\Entity\{Order, OrderGroup, OrderItem};
use App\Order\Entity\Status\{OrderStatus, OrderState};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SendCommand extends Command
{
    protected $signature = 'order:send {date=5m}';
    protected $description = 'Sending orders {optional date: example 5m, 2d}';
    protected ?Collection $orders = null;

    public function handle(SenderOrderData $sender): int
    {
        preg_match('/(\d+)?([m|d])?/i', $this->argument('date'), $matches);
//        $redisClient = Redis::connection('bot')->client();
        try {
            $number = $matches[1] ?: 1;
            $subDate = strtolower($matches[2] ?? 'm') === 'd' ? Carbon::now()->subDays($number) : Carbon::now()->subMinutes($number);
            $this->orders = Order::where('created_at', '>=', $subDate)->whereNot('platform', Platform::WEB)->orderBy('created_at')->get();

            $findOrders = [];
            while ($order = $this->orders->shift()) {
                if (in_array($order->id, $findOrders) or !$sender->checkOrder($order))
                    continue;

                if (!$order2 = $this->findOrderGroup($order)) {
                    if (Carbon::now()->diffInSeconds($order->created_at) >= 30) {
                        $order->sent();
                        $order->save();
                    }

                    continue;
                }

                $findOrders[] = $order2->id;
                if (!$sender->checkOrder($order2))
                    continue;

                $tmpOrder = ($order->delivery_id === 2) ? $this->unionOrders($order, $order2) : $this->unionOrders($order2, $order);

                $order->addStatus(OrderStatus::STATUS_SEND);
                $order2->addStatus(OrderStatus::STATUS_SEND);

                $orderNumber = config('data.orderStartNumber') + $tmpOrder->id;

                $group = OrderGroup::create(['order_1c_id' => $orderNumber]);
                $group->orders()->saveMany([$order, $order2]);

                try {
                    $response = simplexml_load_string($sender->send($tmpOrder));

                    if (isset($response->errors->error->code))
                        throw new \DomainException("Номер заказа: {$orderNumber}. {$response->errors->error->message}");

                    if (isset($response->success->order_id)) {
                        $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
                        $order2->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
                    }
                }
                catch (\Exception $e) {
                    $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);
                    $order2->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);

//                    $redisClient->publish('bot:error', json_encode([
//                        'file' => self::class . ' (' . $e->getLine() . ')',
//                        'message' => $e->getMessage()
//                    ], JSON_UNESCAPED_UNICODE));
                }
                finally {
                    $order->save();
                    $order2->save();
                }
            }
        } catch (\Exception $e) {
//            $redisClient->publish('bot:error', json_encode([
//                'file' => self::class . ' (' . $e->getLine() . ')',
//                'message' => $e->getMessage()
//            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function unionOrders(Order $pickupOrder, Order $bookingOrder): Order
    {
        $newOrder = clone $pickupOrder;
        $newOrderItems = clone $pickupOrder->items;
        foreach ($bookingOrder->items as $item) {
            if ($newItem = $newOrderItems->first(fn(OrderItem $item2) => $item2->product_id === $item->product_id))
                $newItem->quantity += $item->quantity;
            else
                $newOrderItems->add($item);
        }

        $newOrder->items = $newOrderItems;
        $newOrder->cost += $bookingOrder->cost;

        return $newOrder;
    }

    private function findOrderGroup(Order $order): ?Order
    {
        return $this->orders->first(fn(Order $item) => (
            $order->id != $item->id and $order->phone === $item->phone and $order->store_id === $item->store_id
            and $order->created_at->diffInSeconds($item->created_at) < 15
        ));
    }
}
