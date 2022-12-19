<?php

namespace App\UseCases\Order;

use App\Helper;
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
    public function __construct(private readonly CalculateService $service) {}

    public function checkoutWeb(Requests\Order\CheckoutRequest $request): Order
    {
        $data = $request->validated();
        $order = Order::create(
            Store::find($data['store']),
            Payment::find($data['payment'] ?: 2),
            $data['price'],
            Delivery::find($data['delivery'] ?: 2)
        );

        $user = $request->user();
        $order->user()->associate($user);
        $order->setUserInfo($user->first_name, $user->phone, $user->email);

        DB::transaction(function () use ($order, $data) {
            $items = new Collection();
            $offers = new Collection();
            foreach ($data['items'] as $item) {
                $offer = Offer::where('store_id', $data['store'])->where('product_id', $item['id'])->first();
                $offer->checkout($item['quantity']);
                $offers->add($offer);
                $items->add(OrderItem::create($item['id'], $item['price'], $item['quantity']));
            }

            $order->save();
            $order->items()->saveMany($items);

            if ($order->delivery->isType(Delivery::TYPE_DELIVERY)) {
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

        if ($order->payment->isType(Payment::TYPE_CASH)) {
            $order->sent();
            $order->save();
        }

        return $order;
    }

    public function checkoutMobile(Requests\Mobile\CheckoutRequest $request): array
    {
        $orders = [];
        foreach ($request->validated('orders') as $data) {
            $store = Store::find($data['pickupLocationId']);
            $city = City::where('name', Helper::trimPrefixCity($data['city']))->first();
            $payment = Payment::find((int)explode('/', $data['payment'])[1]);
            $phone = str_replace('+', '', $data['phone']);
            $user = User::where('phone', $phone)->first(); // User::find($data['externalUserId']),
            $tmp = $this->service->handle($data['items'], $city, $store?->id, (int)explode('/', $data['delivery'])[1]);

            foreach ($tmp['data'] as $key => $item) {
                try {
                    $order = Order::create(Store::find($key), $payment, $item['totalPrice'], Delivery::find($item['delivery']), $data['deliveryComment'] ?? null);
                    if ($user) $order->user()->associate($user);
                    $order->setUserInfo($data['name'], $phone, $data['email'] ?? null);

                    DB::transaction(function () use ($order, $data, $item, $city) {
                        $orderItems = $this->checkout($item['items'], $order->store_id);
                        if (!$orderItems->count()) throw new \DomainException('Нет товаров в наличии!');

                        $order->save();
                        $order->items()->saveMany($orderItems);

                        if ($order->delivery->isType(Delivery::TYPE_DELIVERY)) {
                            $delivery = OrderDelivery::create(
                                $data['entrance'] ?? null,
                                $data['floor'] ?? null,
                                $data['addressData']['apt'] ?? null,
                                $data['service_to_door'] ?? false
                            );

                            $location = Location::firstOrCreate([
                                'city_id' => $city->id,
                                'street' => $data['addressData']['street'],
                                'house' => $data['addressData']['house']
                            ]);

                            $delivery->location()->associate($location);
                            $order->orderDelivery()->save($delivery);
                        }

                        if ($order->payment->isType(Payment::TYPE_CASH)) {
                            $order->sent();
                            $order->save();
                        }
                    });

                    $orders[] = [
                        'id' => (string)$order->id,
                        'uuid' => $data['uuid'],
                        'success' => true,
                        'price' => $order->cost,
                        'items' => $order->items->map(function (OrderItem $item) use ($data, $order) {
                            $tmp = $data['items'][0];
                            foreach ($data['items'] as $item2) {
                                if ($item2['privateId'] == $item->product_id)
                                    $tmp = $item2;
                            }

                            return [
                                'id' => $tmp['id'],
                                'privateId' => $item->product_id,
                                'configurationId' => $item->product_id,
                                'name' => $item->product->name,
                                'price' => $item->price,
                                'quantity' => $item->quantity,
                                'discount' => 0,
                                'subtotal' => $item->getCost(),
                                'deliveryGroup' => (string)$order->delivery_id
                            ];
                        })
                    ];
                }
                catch (\DomainException $e) {
                    $orders[] = [
                        'id' => null,
                        'uuid' => $data['uuid'],
                        'success' => false,
                        'errorCode' => $e->getCode(),
                        'errorMessage' => $e->getMessage(),
                        'price' => $data['price'],
                        'items' => $data['items']
                    ];
                }
            }

            if (count($tmp['notItems'])) {
                $orders[] = [
                    'id' => null,
                    'uuid' => $data['uuid'],
                    'success' => false,
                    'errorCode' => 0,
                    'errorMessage' => 'Нет в наличии',
                    'price' => $data['price'],
                    'items' => $tmp['notItems']
                ];
            }
        }

        return $orders;
    }

    private function checkout(array $items, string $storeId): Collection
    {
        $orderItems = new Collection();
        foreach ($items as $item) {
            $offer = Offer::where('store_id', $storeId)->where('product_id', $item['id'])->first();

            $offer->checkout($item['quantity']);
            $offer->save();
            $orderItems->add(OrderItem::create($offer->product_id, $item['price'], $item['quantity']));
        }

        return $orderItems;
    }

    public function getStores(Request $request): array
    {
        $carts = $request->collect();
        if (!count($carts))
            throw new \DomainException('Нет товаров в корзине');

        $stores = [];
        Offer::whereIn('product_id', $carts->keys())
            ->whereCity($request->cookie('city', City::find(1)?->name))
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
