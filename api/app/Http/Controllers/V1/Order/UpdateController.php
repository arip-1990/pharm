<?php

namespace App\Http\Controllers\V1\Order;

use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use App\UseCases\Order\RefundService;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UpdateController extends Controller
{
    public function __construct(private readonly RefundService $service) {}

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

    public function handle(): Response
    {
        $xml = file_get_contents("php://input");
        $xml = simplexml_load_string($xml);
        if(!$this->isValidOrderXML($xml))
            return response($this->orderError('Неверный XML', 1), 500);

        $id = intval($xml->order->id) - config('data.orderStartNumber');
        if (!$order = Order::find($id))
            return response($this->orderError('Не найден заказ №' . $id . '!', 2, $id), 500);

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

        return new Response($this->orderSuccess($id), headers: ['Content-Type' => 'application/xml']);
    }
}
