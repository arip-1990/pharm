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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
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
            try {
                $order = Order::create(
                    Store::find($data['pickupLocationId']),
                    Payment::find((int)explode('/', $data['payment'])[1]),
                    $data['price'],
                    Delivery::find((int)explode('/', $data['delivery'])[1]),
                    $data['deliveryComment'] ?? null
                );

                // User::find($data['externalUserId']),
                $phone = str_replace('+', '', $data['phone']);
                if ($user = User::where('phone', $phone)->first())
                    $order->user()->associate($user);

                $order->setUserInfo($data['name'], $phone, $data['email'] ?? null);

                $items = [];
                DB::transaction(function () use ($order, $data, &$items) {
                    $tmp = $this->checkout($data['items'], $order->store_id);
                    $items = $tmp['items2'];

                    if (count($tmp['items'])) {
                        $order->save();
                        $order->items()->saveMany($tmp['items']);

                        if ($order->delivery->isType(Delivery::TYPE_DELIVERY)) {
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
                    }
                });

                $order2 = null;
                if (count($items)) {
                    $city = $order->store->location->city;
                    $locationIds = Location::whereIn('city_id', $city->children()->pluck('id')->add($city->id))->pluck('id');
                    $stores = Store::whereHas('offers', function (Builder $query) use ($items) {
                        $query->whereIn('product_id', array_column($items, 'productId'))
                        ->where('quantity', '>', 0);
                    })->whereIn('id', config('data.mobileStores'))->whereIn('location_id', $locationIds)->get();

                    if ($stores->count()) {
                        $store = $stores[0];

                        DB::transaction(function () use ($order, $order2, $data, $store, $items) {
                            if ($order->delivery->isType(Delivery::TYPE_PICKUP))
                                $delivery = Delivery::where('type', Delivery::TYPE_DELIVERY)->first();
                            else $delivery = Delivery::where('type', Delivery::TYPE_PICKUP)->first();

                            $order2 = Order::create($store, $order->payment, $data['price'], $delivery, $data['deliveryComment'] ?? null);
                            $tmp = $this->checkout($items, $store->id);

                            if (count($tmp['items'])) {
                                $order2->save();
                                $order2->items()->saveMany($tmp['items']);

                                if ($order->delivery->isType(Delivery::TYPE_DELIVERY)) {
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
                                    $order2->orderDelivery()->save($delivery);
                                }
                            }
                        });
                    }
                }

                if ($order->payment->isType(Payment::TYPE_CASH)) {
                    $order->sent();
                    $order->save();

                    if ($order2) {
                        $order2->sent();
                        $order2->save();
                    }
                }

                if ($order->id) {
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
                                'discount' => $tmp['discount'],
                                'subtotal' => $item->getCost(),
                                'deliveryGroup' => $order->delivery_id
                            ];
                        })
                    ];
                }

                if ($order2) {
                    $orders[] = [
                        'id' => (string)$order2->id,
                        'uuid' => $data['uuid'],
                        'success' => true,
                        'price' => $order2->cost,
                        'items' => $order2->items->map(function (OrderItem $item) use ($data, $order2) {
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
                                'discount' => $tmp['discount'],
                                'subtotal' => $item->getCost(),
                                'deliveryGroup' => $order2->delivery_id
                            ];
                        })->toArray()
                    ];
                }

                if (!count($orders)) throw new \DomainException('Нет товаров в наличии!');
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

        return $orders;
    }

    private function checkout(array $items, string $storeId): array
    {
        $notItems = [];
        $orderItems = [];
        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['productId'];
            $quantity = $item['quantity'];

            $offer = Offer::where('quantity', '>', 0)->where('store_id', $storeId)
                ->where('product_id', $productId)->first();
            if (!$offer) {
                $notItems[] = ['productId' => $productId, 'quantity' => $quantity];
                continue;
            }

            if ($offer->quantity < $quantity) {
                $notItems[] = ['productId' => $productId, 'quantity' => $quantity - $offer->quantity];
                $quantity = $offer->quantity;
            }

            $offer->checkout($quantity);
            $offer->save();
            $orderItems[] = OrderItem::create($productId, $offer->price, $quantity);
        }

        return ['items' => $orderItems, 'items2' => $notItems];
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
