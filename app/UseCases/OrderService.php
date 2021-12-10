<?php

namespace App\UseCases;

use App\Entities\CartItem;
use App\Entities\Offer;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Store;
use App\Http\Requests\Catalog\CheckoutRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function __construct(private CartService $cartService) {}

    public function checkout(CheckoutRequest $request): Order
    {
        if ($request['delivery'] === Order::DELIVERY_TYPE_COURIER and $request['payment'] == Order::PAYMENT_TYPE_CASH)
            throw new \DomainException('Не возможно оплатить наличными');

        $this->cartService->setStore(Store::query()->find($request['store']));
        $offers = [];
        $items = $this->cartService->getItems()->map(function (CartItem $item) use (&$offers) {
            /** @var Offer $offer */
            $offer = Offer::query()->where('store_id', $this->cartService->getStore()->id)->where('product_id', $item->product_id)->first();
            $offer->checkout($item->quantity);
            $offers[] = $offer;

            return OrderItem::create($item->product_id, $item->getAmount($offer->store), $item->quantity);
        });

        $order = Order::create(
            Auth::id(),
            $this->cartService->getStore()->id,
            $request['payment'],
            $this->cartService->getTotalAmount(),
            $request['delivery'],
        );
        $order->save();

        $order->items()->saveMany($items);

//        if ($request['delivery'] === Order::DELIVERY_TYPE_COURIER) {
//            $order->setDeliveryInfo(Delivery::create(
//                $request->delivery->city,
//                [
//                    'street' => $request->delivery->street,
//                    'house' => $request->delivery->house,
//                    'entrance' => $request->delivery->entrance,
//                    'floor' => $request->delivery->floor,
//                    'apartment' => $request->delivery->apartment
//                ],
//                $request->delivery->service_to_door
//            ), $request->delivery_type);
//        }

        foreach ($offers as $offer) $offer->save();

        foreach ($request->session()->get('oldCartItems', new Collection()) as $item) {
            try {
                $newItem = $this->cartService->getItem($item->product_id);

                $quantity = $item->quantity - $newItem->quantity;
                if ($quantity > 0)
                    $this->cartService->set($item->product_id, $quantity);
                else
                    $this->cartService->remove($item->product_id);
            }
            catch (\DomainException $exception) {}
        }

        return $order;
    }

    public function paymentSberbank(Order $order, string $redirectUrl): string
    {
        $curl = curl_init();
        $config = config('data.pay.sber.test');
        $url = $config['url'];
        $username = $config['username'];
        $password = $config['password'];

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'userName'      => $username,
                'password'      => $password,
                'orderNumber'   => $config['prefix_number'] . $order->id,
                'amount'        => $order->getTotalCost() * 100,
                'returnUrl'     => $redirectUrl,
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if(isset($response['errorCode']))
            throw new \DomainException('Не удалось создать форму оплаты. ' . $response['errorMessage']);

        $order->pay($response['orderId']);
        $order->save();
        return $response['formUrl'];
    }
}
