<?php

namespace App\Http\Controllers\Order;

use App\Models\Order;
use App\Models\Status\OrderStatus;
use App\UseCases\Order\RefundService;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function __construct(private readonly RefundService $service) {}

    private function isValidEditOrderXML($xml): bool
    {
        if(isset($xml->order->id) && isset($xml->order->status))
            return true;
        return false;
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

    public function handle(): Response
    {
        $xml = file_get_contents("php://input");
        $xml = simplexml_load_string($xml);
        if(!$this->isValidEditOrderXML($xml))
            return response($this->orderError('Неверный XML', 1), 500);

        $id = intval($xml->order->id) - config('data.orderStartNumber');
        /** @var Order $order */
        if (!$order = Order::query()->find($id)) {
            return response($this->orderError('Не найден заказ №' . $id . '!', 2, $id), 500);
        }

        switch ($status = (string)$xml->order->status) {
            case OrderStatus::STATUS_ASSEMBLED_PHARMACY:
                $order->assembled();
                break;
            case OrderStatus::STATUS_CANCELLED:
            case OrderStatus::STATUS_DISBANDED:
            case OrderStatus::STATUS_RETURN_BY_COURIER:
                $order->cancel(status: OrderStatus::from($status));
                $this->service->fullRefund($order);
                break;
            default:
                $order->addStatus(OrderStatus::from($status));
        }

        if (isset($xml->order->products->product) and ($order->isAssembled() or $order->isReceived()))
            $this->service->partlyRefund($order, $xml->order->products->product);

        try {
            $order->save();
            return response($this->orderSuccess($id));
        }
        catch (\RuntimeException $e) {
            return response($this->orderError($e->getMessage(), 2, $id), 500);
        }
    }
}
