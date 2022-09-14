<?php

namespace App\UseCases\Order;

use App\Http\Requests;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\City;
use App\Models\Location;
use App\Models\OrderDelivery;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Street;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function checkoutWeb(Requests\Order\CheckoutRequest $request): Order
    {
        $data = $request->validated();
        $order = Order::create(Auth::id(), $data['store'], $data['payment'], $data['price'], $data['delivery']);

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

            $order->save();
            $order->items()->saveMany($items);

            if ($data['delivery'] == Order::DELIVERY_TYPE_COURIER) {
                $delivery = OrderDelivery::create(
                    $data['entrance'] ?? null,
                    $data['floor'] ?? null,
                    $data['apartment'] ?? null,
                    $data['service_to_door']
                );

                $street = Street::query()->firstOrCreate(['name' => $data['street'], 'house' => $data['house']]);
                $location = Location::query()->firstOrCreate(['city_id' => 1, 'street_id' => $street->id]);

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
            $paymentType = (int)(explode('/', $data['payment'])[0] === 'card');
            $deliveryType = (int)(explode('/', $data['delivery'])[1] === 'regular');
            $order = Order::create($data['externalUserId'], $data['pickupLocationId'], $paymentType, $data['price'], $deliveryType);

            try {
                DB::transaction(function () use ($order, $data, $deliveryType) {
                    $items = new Collection();
                    $offers = new Collection();
                    foreach ($data['items'] as $item) {
                        /** @var Offer $offer */
                        if (!$offer = Offer::query()->where('store_id', $data['pickupLocationId'])->where('product_id', $item['privateId'])->first())
                            throw new \DomainException('Товар не найден');

                        $offer->checkout($item['quantity']);
                        $offers->add($offer);
                        $items->add(OrderItem::create($item['privateId'], $item['price'], $item['quantity']));
                    }

                    $order->save();
                    $order->items()->saveMany($items);

                    if ($deliveryType == Order::DELIVERY_TYPE_COURIER) {
                        $delivery = OrderDelivery::create(
                            $data['entrance'] ?? null,
                            $data['floor'] ?? null,
                            $data['addressData']['apt'] ?? null,
                            $data['service_to_door'] ?? false
                        );

                        $street = Street::query()->firstOrCreate(['name' => $data['addressData']['street'], 'house' => $data['addressData']['house']]);
                        $location = Location::query()->firstOrCreate(['city_id' => 1, 'street_id' => $street->id]);

                        $delivery->location()->associate($location);
                        $order->orderDelivery()->save($delivery);
                    }

                    $offers->each(fn(Offer $offer) => $offer->save());
                });
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

                continue;
            }

            $orders[] = [
                'id' => $order->id,
                'uuid' => $data['uuid'],
                'success' => true,
                'price' => $order->cost,
                'items' => $data['items']
            ];
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
