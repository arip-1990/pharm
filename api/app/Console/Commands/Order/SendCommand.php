<?php

namespace App\Console\Commands\Order;

use App\Order\UseCase\GenerateDataService;
use App\Order\Entity\{Order, OrderGroup, OrderItem, Payment};
use App\Order\Entity\Status\{OrderStatus, OrderState};
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SendCommand extends Command
{
    protected $signature = 'order:send {date=5m}';
    protected $description = 'Sending orders {optional date: example 5m, 2d}';

    public function handle(): int
    {
        preg_match('/(\d+)?([m|d])?/i', $this->argument('date'), $matches);
        $redisClient = Redis::connection('bot')->client();
        try {
            $number = (int)$matches[1] ?: 5;

            $subDate = strtolower($matches[2] ?? 'm') === 'd' ? Carbon::now()->subDays($number) : Carbon::now()->subMinutes($number);
            $orders = Order::where('created_at', '>=', $subDate)->get();
            /** @var Order $order */
            foreach ($orders as $order) {
                if ($order->isSent() or $order->isStatusSuccess(OrderStatus::STATUS_PROCESSING) or ($order->payment->isType(Payment::TYPE_CARD) and !$order->isPay()))
                    continue;

                /** @var Order $order2 */
                if (!$order2 = $orders->first(fn(Order $item) => (
                    $item->id !== $order->id and $item->phone === $order->phone and $item->store_id === $order->store_id
                    and $item->delivery_id !== $order->delivery_id and $item->created_at->diffInMinutes($order->created_at) < 1
                ))) {
                    if (Carbon::now()->diffInMinutes($order->created_at) >= 1) {
                        $order->sent();
                        $order->save();
                    }
                    continue;
                }

                if ($order2->isSent() or $order->isStatusSuccess(OrderStatus::STATUS_PROCESSING) or ($order2->payment->isType(Payment::TYPE_CARD) and !$order2->isPay()))
                    continue;

                $tmpOrder = ($order->delivery_id === 2) ? $this->unionOrders($order, $order2) : $this->unionOrders($order2, $order);

                $order->addStatus(OrderStatus::STATUS_SEND);
                $order2->addStatus(OrderStatus::STATUS_SEND);

                $orderNumber = config('data.orderStartNumber') + $tmpOrder->id;

                $group = OrderGroup::create(['order_1c_id' => $orderNumber]);
                $group->orders()->saveMany([$order, $order2]);

                try {
                    $response = simplexml_load_string($this->orderSend($tmpOrder));

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

                    $redisClient->publish('bot:error', json_encode([
                        'file' => self::class . ' (' . $e->getLine() . ')',
                        'message' => $e->getMessage()
                    ], JSON_UNESCAPED_UNICODE));
                }
                finally {
                    $order->save();
                    $order2->save();
                }
            }
        } catch (\Exception $e) {
            $redisClient->publish('bot:error', json_encode([
                'file' => self::class . ' (' . $e->getLine() . ')',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function unionOrders(Order $pickupOrder, Order $bookingOrder): Order
    {
        $newOrder = clone $pickupOrder;
        $newOrderItems = clone $pickupOrder->items;
        foreach ($bookingOrder->items as $item) {
            if ($newItem = $newOrderItems->first(fn(OrderItem $item2) => $item2->product_id === $item->product_id)) {
                $newItem->quantity += $item->quantity;
            }
            else {
                $newOrderItems->add($item);
            }
        }

        $newOrder->items = $newOrderItems;
        $newOrder->cost += $bookingOrder->cost;

        return $newOrder;
    }

    private function orderSend(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');

        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);

        $response = $client->post($config['urls'][5], ['body' => $service->generateSenData(Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
