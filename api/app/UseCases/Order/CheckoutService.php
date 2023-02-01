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
use App\Models\Status\OrderState;
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

        $order->changeState(OrderState::STATE_SUCCESS);
        if ($order->payment->isType(Payment::TYPE_CASH)) $order->sent();

        $order->save();

        return $order;
    }

    public function checkoutMobile(Requests\Mobile\CheckoutRequest $request): array
    {
        $data = [];
        foreach ($request->validated('orders') as $item) {
            $phone = str_replace('+', '', $item['phone']);
            $order = Order::create(
                Store::find($item['pickupLocationId']),
                Payment::find((int)explode('/', $item['payment'])[1]),
                Delivery::find((int)explode('/', $item['delivery'])[1]),
                $item['deliveryComment'] ?? null
            );

            try {
                // User::find($data['externalUserId'])
                if ($user = User::where('phone', $phone)->first()) $order->user()->associate($user);
                $order->setUserInfo($item['name'], $phone, $item['email'] ?? null);

                DB::transaction(function () use ($item, $order) {
                    $orderItems = $this->checkout($item['items'], $order->store_id);

                    $order->setCost($orderItems->sum(fn (OrderItem $item) => $item->getCost()));
                    $order->save();
                    $order->items()->saveMany($orderItems);
                });

                $order->changeState(OrderState::STATE_SUCCESS);
                if ($order->payment->isType(Payment::TYPE_CASH)) $order->sent();

                $data[] = [
                    'id' => (string)$order->id,
                    'uuid' => $item['uuid'],
                    'success' => true,
                    'price' => $order->cost,
                    'items' => $order->items->map(function (OrderItem $orderItem) use ($item, $order) {
                        $tmp = $item['items'][0];
                        foreach ($item['items'] as $item2) {
                            if ($item2['privateId'] == $orderItem->product_id)
                                $tmp = $item2;
                        }

                        return [
                            'id' => $tmp['id'],
                            'privateId' => $orderItem->product_id,
                            'configurationId' => $orderItem->product_id,
                            'name' => $orderItem->product->name,
                            'price' => $orderItem->price,
                            'quantity' => $orderItem->quantity,
                            'discount' => 0,
                            'subtotal' => $orderItem->getCost(),
//                            'deliveryGroups' => $order->delivery_id === 2 ? ['2', '3'] : ['3']
                        ];
                    })
                ];
            }
            catch (\DomainException $e) {
                $order->changeState(OrderState::STATE_ERROR);

                $data[] = [
                    'id' => null,
                    'uuid' => $item['uuid'],
                    'success' => false,
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                    'price' => $item['price'],
                    'items' => $item['items']
                ];
            }

            $order->save();
        }

        return $data;
    }

    private function checkout(array $items, string $storeId, bool $isBooking = false): Collection
    {
        $orderItems = new Collection();
//        if (!$isBooking) {
//            foreach ($items as $item) {
//                $productId = $item['privateId'] ?? $item['id'];
//                if (!$offer = Offer::where('store_id', $storeId)->where('product_id', $productId)->first())
//                    throw new \DomainException('Нет в наличии!');
//
//                $offer->checkout($item['quantity']);
//                $offer->save();
//                $orderItems->add(OrderItem::create($productId, $item['price'], $item['quantity']));
//            }
//        }
//        else {
//            foreach ($items as $item) {
//                $productId = $item['privateId'] ?? $item['id'];
//                $orderItems->add(OrderItem::create($productId, $item['price'], $item['quantity']));
//            }
//        }

        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['id'];
            try {
                $offer = Offer::where('store_id', $storeId)->where('product_id', $productId)->where('quantity', '>', 0)->first();
                $offer?->checkout($item['quantity']);
                $offer?->save();
            }
            catch (\DomainException $e) {}

            $orderItems->add(OrderItem::create($productId, $item['price'], $item['quantity']));
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
