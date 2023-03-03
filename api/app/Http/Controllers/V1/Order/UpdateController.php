<?php

namespace App\Http\Controllers\V1\Order;

use App\Models\Product;
use App\Order\Entity\Delivery;
use Illuminate\Http\JsonResponse;
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

            $order = $this->repository->getById(intval($xml->order->id) - config('data.orderStartNumber'));

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

//            if (isset($xml->order_transfer->id)) {
//                if ($order->delivery_id !== 3) {
//                    $order2 =
//                }
//                foreach ($xml->order_transfer->products as $item) {
//                    $product = Product::where('code', (int)$item->product->code)->first();
//                }
//            }
            $order->save();

            return new Response($this->orderSuccess($order->id), headers: ['Content-Type' => 'application/xml']);
        }
        catch (\Exception | \DomainException $exception) {
            $status = $exception instanceof \DomainException ? 404 : 500;

            return new Response($this->orderError($exception->getMessage(), $exception->getCode()), $status);
        }
    }
}
