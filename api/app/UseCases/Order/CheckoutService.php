<?php

namespace App\UseCases\Order;

use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\CartItem;
use App\Models\City;
use App\Models\OrderDelivery;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Http\Requests\Catalog\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function checkout(CheckoutRequest $request): Order
    {
        $this->cartService->setStore(Store::query()->find($request['store']));
        $order = Order::create(
            Auth::id(),
            $this->cartService->getStore()->id,
            $request->get('payment'),
            $this->cartService->getTotalAmount(),
            $request->get('delivery'),
        );

        DB::transaction(function () use ($order, $request) {
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

        if ($request->get('delivery') == Order::DELIVERY_TYPE_COURIER) {
            $delivery = OrderDelivery::create(
                $request->get('city'),
                [
                    'street' => $request->get('street'),
                    'house' => $request->get('house'),
                    'entrance' => $request->get('entrance'),
                    'floor' => $request->get('floor'),
                    'apartment' => $request->get('apartment')
                ],
                $request->get('service_to_door', false)
            );
            $order->delivery()->save($delivery);
        }

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
                'orderNumber'   => $order->id,
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

    public function getStores(Request $request): array
    {
        $carts = $request->collect();
        if (!count($carts))
            throw new \DomainException('Нет товаров в корзине');

        $stores = [];
        Offer::query()->whereIn('product_id', $carts->keys())
            ->whereCity($request->cookie('city', City::query()->find(1)?->name))
            ->each(function (Offer $offer) use ($carts, &$stores) {
                $cartQuantity = (int)$carts[$offer->product_id];
                $stores[$offer->store_id]['store'] = new StoreResource($offer->store);
                $stores[$offer->store_id]['products'][] = [
                    'price' => $offer->price,
                    'quantity' => min($cartQuantity, $offer->quantity),
                    'product' => new ProductResource($offer->product)
                ];
            });
        usort($stores, function ($a, $b) {
            $res = count($b['products']) - count($a['products']);
            if ($res) return $res;
            else {
                $price_a = 0;
                $price_b = 0;
                $quantity_a = 0;
                $quantity_b = 0;
                for ($i = 0; $i < count($a['products']); $i++) {
                    $quantity_a = $a['products'][$i]['quantity'];
                    $quantity_b = $b['products'][$i]['quantity'];
                    $price_a += $quantity_a * $a['products'][$i]['price'];
                    $price_b += $quantity_b * $b['products'][$i]['price'];
                }
                $res = $quantity_b - $quantity_a;
                return $res ?: $price_a - $price_b;
            }
        });

        return $stores;
    }
}
