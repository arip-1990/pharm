<?php

namespace App\Http\Controllers\V1\Order;

use App\Order\Entity\{Delivery, Order, OrderGroup, OrderItem, OrderRepository};
use App\Order\Entity\Status\OrderStatus;
use App\Order\UseCase\RefundService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function __construct(private readonly RefundService $service, private readonly OrderRepository $repository) {}

    private function isValidOrderXML($xml): bool
    {
        return isset($xml->order->id) && isset($xml->order->status);
    }

    private function orderSuccess(int $orderId): string
    {
        return '
            <orders_result>
                <success>
                    <order_id>' . $orderId . '</order_id>
                </success>
            </orders_result>';

    }

    private function orderError(string $errorMessage, int $errorCode = -1, int $orderId = -1): string
    {
        return '
            <orders_result>
                <error>
                    <order_id>' . $orderId . '</order_id>
                    <code>' . $errorCode . '</code>
                    <message>' . $errorMessage . '</message>
                </error>
            </orders_result>';
    }

    public function handle(Request $request)
    {
        try {
            $xml = simplexml_load_string($request->getContent());
            if(!$this->isValidOrderXML($xml)) throw new \Exception('Неверный XML', 1);

            $order1cId = (int)$xml->order->id;
            $status = OrderStatus::from((string)$xml->order->status);
            $order = $this->repository->getById($order1cId - config('data.orderStartNumber'));

            if (isset($xml->order_transfer->id)) {
                if (!$group = OrderGroup::where('order_1c_id', $order1cId)->first()) {
                    $group = OrderGroup::create(['order_1c_id' => $order1cId]);
                    $order->delivery_id = 2;
                    $order2 = Order::create($order->store, $order->payment, Delivery::find(3));
                    $group->orders()->saveMany([$order, $order2]);
                }
                else {
                    $order = $group->orders->firstWhere('delivery_id', 2);
                    $order2 = $group->orders->firstWhere('delivery_id', 3);
                }

                foreach ($xml->order_transfer->products as $item) {
                    $price = (float)$item->product->price;
                    $quantity = (int)$item->product->quantity;
                    $productId = (string)$item->product->code;
                    $orderItem = $order->items->firstWhere('product_id', $productId);

                    if (!$orderItem2 = $order2->items->firstWhere('product_id', $productId)) {
                        $orderItem2 = OrderItem::create($productId, $price, $quantity);

                        if ($orderItem) {
                            if ($orderItem->quantity > $orderItem2->quantity)
                                $orderItem->update(['quantity' => $orderItem->quantity - $orderItem2->quantity]);
                            else
                                $orderItem->delete();
                        }

                        $order2->items()->save($orderItem2);
                    }
                    elseif ($quantity != $orderItem2->quantity) {
                        if ($orderItem) {
                            if ($quantity > $orderItem2->quantity)
                                $orderItem->update(['quantity' => $orderItem->quantity - ($quantity - $orderItem2->quantity)]);
                            else
                                $orderItem->update(['quantity' => $orderItem->quantity + ($orderItem2->quantity - $quantity)]);
                        }

                        $orderItem2->update(['quantity' => $quantity]);
                    }
                }

                $this->repository->addStatus($order2, OrderStatus::from((string)$xml->order_transfer->status));
                $this->repository->changeState($order2);
                $order2->save();
            }

            $this->repository->addStatus($order, $status);
            if ($status == OrderStatus::STATUS_CANCELLED) {
                $this->service->fullRefund($order);
            }
//            elseif (isset($xml->order->products->product) and ($status == OrderStatus::STATUS_ASSEMBLED or $status == OrderStatus::STATUS_RECEIVED)) {
//                $this->service->partlyRefund($order, $xml->order->products->product);
//            }

            $this->repository->changeState($order, $status);
            $order->save();

            return new Response($this->orderSuccess($order->id), headers: ['Content-Type' => 'application/xml']);
        }
        catch (\Exception | \DomainException $exception) {
            return new Response(
                $this->orderError($exception->getMessage(), (int)$exception->getCode(), $order1cId ?? -1),
                $exception instanceof \DomainException ? 404 : 500,
                headers: ['Content-Type' => 'application/xml']
            );
        }
    }
}
