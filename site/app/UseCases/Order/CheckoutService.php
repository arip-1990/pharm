<?php

namespace App\UseCases\Order;

use App\Models\CartItem;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Http\Requests\Catalog\CheckoutRequest;
use App\UseCases\CartService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(private CartService $cartService) {}

    public function checkout(CheckoutRequest $request): Order
    {
        if ($request['delivery'] === Order::DELIVERY_TYPE_COURIER and $request['payment'] == Order::PAYMENT_TYPE_CASH)
            throw new \DomainException('Не возможно оплатить наличными');

        $this->cartService->setStore(Store::query()->find($request['store']));
        $order = Order::create(
            Auth::id(),
            $this->cartService->getStore()->id,
            $request['payment'],
            $this->cartService->getTotalAmount(),
            $request['delivery'],
        );

        DB::transaction(function () use ($order) {
            $offers = new Collection();
            $items = $this->cartService->getItems()->map(function (CartItem $item) use (&$offers) {
                /** @var Offer $offer */
                $offer = Offer::query()->where('store_id', $this->cartService->getStore()->id)
                    ->where('product_id', $item->product_id)->first();
                $offer->checkout($item->quantity);
                $offers->add($offer);

                return OrderItem::create($item->product_id, $item->getAmount($offer->store), $item->quantity);
            });

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

            $offers->each(fn(Offer $offer) => $offer->save());
            $this->cartService->clear();
        });

        return $order;
    }

    public function paymentSberbank(Order $order, string $redirectUrl): string
    {
        $curl = curl_init();
        $config =  config('app.env') === 'production' ? config('data.pay.sber.prod') : config('data.pay.sber.test');
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
