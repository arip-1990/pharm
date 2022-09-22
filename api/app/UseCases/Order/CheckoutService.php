<?php

namespace App\UseCases\Order;

use App\Http\Requests;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\City;
use App\Models\Delivery;
use App\Models\Location;
use App\Models\OrderDelivery;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function checkoutWeb(Requests\Order\CheckoutRequest $request): Order
    {
        $data = $request->validated();
        $order = Order::create(
            $request->user(),
            Store::find($data['store']),
            Payment::find($data['payment'] ?: 2),
            $data['price'],
            Delivery::find($data['delivery'] ?: 2)
        );

        DB::transaction(function () use ($order, $data) {
            $items = new Collection();
            $offers = new Collection();
            foreach ($data['items'] as $item) {
                /** @var Offer $offer */
                $offer = Offer::query()->where('store_id', $data['store'])->where('product_id', $item['id'])->first();
                $offer->checkout($item['quantity']);
                $offers->add($offer);
                $items->add(OrderItem::create($item['id'], $item['price'], $item['quantity']));
            }

            if ($order->payment->equalType(Payment::TYPE_CASH)) $order->sent();

            $order->save();
            $order->items()->saveMany($items);

            if ($order->delivery->equalType(Delivery::TYPE_DELIVERY)) {
                $delivery = OrderDelivery::create(
                    $data['entrance'] ?? null,
                    $data['floor'] ?? null,
                    $data['apartment'] ?? null,
                    $data['service_to_door']
                );

                $city = City::find(1);
                $location = Location::whereIn('city_id', $city->children()->pluck('id')->add($city->id))
                    ->firstOrCreate(['street' => $data['street'], 'house' => $data['house']], ['city_id' => $city->id]);

                $delivery->location()->associate($location);
                $order->orderDelivery()->save($delivery);
            }

            $offers->each(fn(Offer $offer) => $offer->save());
        });

        return $order;
    }

    public function checkoutMobile(Requests\Mobile\CheckoutRequest $request): array
    {
        $orders = [];
        foreach ($request->validated('orders') as $data) {
            $order = Order::create(
//                User::find($data['externalUserId']),
                User::where('phone', str_replace('+', '', $data['phone']))->first(),
                Store::find($data['pickupLocationId']),
                Payment::find((int)explode('/', $data['payment'])[1]),
                $data['price'],
                Delivery::find((int)explode('/', $data['delivery'])[1])
            );

            try {
                DB::transaction(function () use ($order, $data) {
                    $items = new Collection();
                    $offers = new Collection();
                    foreach ($data['items'] as $item) {
                        /** @var Offer $offer */
                        if (!$offer = Offer::query()->where('store_id', $order->store_id)
                            ->where('product_id', $item['privateId'])->first())
                            throw new \DomainException('Товар не найден');

                        $offer->checkout($item['quantity']);
                        $offers->add($offer);
                        $items->add(OrderItem::create($item['privateId'], $item['price'], $item['quantity']));
                    }

                    if ($order->payment->equalType(Payment::TYPE_CASH)) $order->sent();

                    $order->save();
                    $order->items()->saveMany($items);

                    if ($order->delivery->equalType(Delivery::TYPE_DELIVERY)) {
                        $delivery = OrderDelivery::create(
                            $data['entrance'] ?? null,
                            $data['floor'] ?? null,
                            $data['addressData']['apt'] ?? null,
                            $data['service_to_door'] ?? false
                        );

                        $location = Location::firstOrCreate([
                            'city_id' => 1,
                            'street' => $data['addressData']['street'],
                            'house' => $data['addressData']['house']
                        ]);

                        $delivery->location()->associate($location);
                        $order->orderDelivery()->save($delivery);
                    }

                    $offers->each(fn(Offer $offer) => $offer->save());
                });

                $orders[] = [
                    'id' => $order->id,
                    'uuid' => $data['uuid'],
                    'success' => true,
                    'price' => $order->cost,
                    'items' => $data['items']
                ];
            }
            catch (\DomainException $e) {
                $orders[] = [
                    'id' => $order->id,
                    'uuid' => $data['uuid'],
                    'success' => false,
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                    'price' => $order->cost,
                    'items' => $data['items']
                ];
            }
        }

        return $orders;
    }

    public function paySber(Order $order, string $redirectUrl): string
    {
        $curl = curl_init();
        $config =  config('app.env') === 'production' ? config('data.pay.sber.prod') : config('data.pay.sber.test');

        curl_setopt_array($curl, [
            CURLOPT_URL => $config['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'userName'      => $config['username'],
                'password'      => $config['password'],
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
