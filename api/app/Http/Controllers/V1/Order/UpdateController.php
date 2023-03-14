<?php

namespace App\Http\Controllers\V1\Order;

use App\Order\Entity\OrderGroup;
use App\Order\Entity\OrderItem;
use Illuminate\Http\Request;
use App\Order\Entity\Status\{OrderStatus, OrderState};
use App\Order\Entity\OrderRepository;
use App\Order\UseCase\RefundService;
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

            $order1cId = intval($xml->order->id);
            if (isset($xml->order_transfer->id)) {
                if ($group = OrderGroup::where('order_1c_id', $order1cId)->first()) {
                    /** @var Order $order2 */
                    $order = $group->orders->whereFirst('delivery_id', 2);
                    $order2 = $group->orders->whereFirst('delivery_id', 3);

                    foreach ($xml->order_transfer->products as $item) {
                        $price = (float)$item->product->price;
                        $quantity = (int)$item->product->quantity;
                        $product = Product::where('code', (int)$item->product->code)->first();
                        $orderItem = $order->items->firstWhere('product_id', $product->id);

                        if (!$orderItem2 = $order2->items->firstWhere('product_id', $product->id)) {
                            $orderItem2 = OrderItem::create($product->id, $price, $quantity);

                            if ($orderItem) {
                                if ($orderItem->quantity > $orderItem2->quantity) {
                                    $orderItem->update(['quantity' => $orderItem->quantity - $orderItem2->quantity]);
                                }
                                else {
                                    $orderItem->delete();
                                }
                            }

                            $order2->items()->save($orderItem2);
                        }
                        else {
                            if ($quantity != $orderItem2->quantity) {
                                if ($orderItem) {
                                    if ($quantity > $orderItem2->quantity) {
                                        $orderItem->update(['quantity' => $orderItem->quantity - ($quantity - $orderItem2->quantity)]);
                                    }
                                    else {
                                        $orderItem->update(['quantity' => $orderItem->quantity + ($orderItem2->quantity - $quantity)]);
                                    }
                                }

                                $orderItem2->update(['quantity' => $quantity]);
                            }
                        }
                    }

                    $order2->addStatus(OrderStatus::from((string)$xml->order_transfer->status), OrderState::STATE_SUCCESS);
                    $order2->save();
                }
                else {
                    $order = $this->repository->getById($order1cId - config('data.orderStartNumber'));
                }
            }
            else {
                $order = $this->repository->getById($order1cId - config('data.orderStartNumber'));
            }

            switch ($status = OrderStatus::from((string)$xml->order->status)) {
                case OrderStatus::STATUS_ASSEMBLED:
                    $order->assembled();
                    break;
                case OrderStatus::STATUS_CANCELLED:
                    $order->cancel();
                    $this->service->fullRefund($order);
                    break;
                default:
                    $order->addStatus($status);
            }

            if (isset($xml->order->products->product) and ($order->isAssembled() or $order->isReceived()))
                $this->service->partlyRefund($order, $xml->order->products->product);

            $order->changeStatusState($status, OrderState::STATE_SUCCESS);
            $order->save();
            return new Response($this->orderSuccess($order->id), headers: ['Content-Type' => 'application/xml']);
        }
        catch (\Exception | \DomainException $exception) {
            $status = $exception instanceof \DomainException ? 404 : 500;

            return new Response($this->orderError($exception->getMessage(), (int)$exception->getCode()), $status);
        }
    }
}
