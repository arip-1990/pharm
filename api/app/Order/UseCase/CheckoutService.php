<?php

namespace App\Order\UseCase;

use App\Exceptions\OrderException;
use App\Helper;
use App\Http\Requests;
use App\Models\User;
use App\Product\Entity\Offer;
use Illuminate\Support\Facades\Redis;
use App\Http\Resources\{ProductResource, StoreResource};
use App\Order\Entity\{Delivery, Order, OrderDelivery, OrderItem, Payment};
use App\Order\Entity\Status\OrderState;
use App\Store\Entity\{City, Location, Store};
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
            Delivery::find($data['delivery'] ?: 2)
        );

        $order->setCost($data['price']);

        $user = $request->user();
        $order->user()->associate($user);
        $order->setUserInfo($user->first_name, $user->phone, $user->email);
        $order->save();

        $this->checkOrderId($order->id);

        DB::transaction(function () use ($order, $data) {
            $order->items()->saveMany($this->checkout($data['items']));
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

            $order->changeState(OrderState::STATE_SUCCESS);
            if ($order->payment->isType(Payment::TYPE_CASH))
                $order->sent();

            $order->save();
        });

        return $order;
    }

    public function checkoutMobile(Requests\Mobile\CheckoutRequest $request): array
    {
        $data = [];
        $platform = $request->input('device.platform', 'android');
        foreach ($request->validated('orders') as $item) {
            if (!$city = City::where('name', Helper::trimPrefixCity($item['city'] ?? $item['addressData']['settlement']))->first())
                throw new OrderException('Город неизвестен');

            $order = Order::create(
                Store::find($item['pickupLocationId']),
                Payment::find((int)explode('/', $item['payment'])[1]),
                Delivery::find((int)explode('/', $item['delivery'])[1]),
                $item['deliveryComment'] ?? null
            );
            $order->setPlatform($platform);

            try {
                $phone = str_replace('+', '', $item['phone']);

                // User::find($data['externalUserId'])
                if ($user = User::where('phone', $phone)->first())
                    $order->user()->associate($user);

                $order->setUserInfo($item['name'], $phone, $item['email'] ?? null);
                $orderItems = $this->checkout($item['items']);

                $order->setCost($orderItems->sum(fn (OrderItem $item) => $item->getCost()));
                $order->save();

                $this->checkOrderId($order->id);

                $order->items()->saveMany($orderItems);
                $order->changeState(OrderState::STATE_SUCCESS);

                if (!$city->isBookingAvailable() and $order->payment->isType(Payment::TYPE_CASH))
                    $order->sent();

                $tmp = [
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
                            'deliveryGroups' => $order->isAvailableItem($orderItem) ? ['2', '3'] : ['3']
                        ];
                    })
                ];
            }
            catch (\Exception $e) {
                $order->changeState(OrderState::STATE_ERROR);

                $tmp = [
                    'uuid' => $item['uuid'],
                    'success' => false,
                    'errorCode' => $e->getCode(),
                    'errorMessage' => $e->getMessage(),
                    'price' => $item['price'],
                    'items' => $item['items']
                ];
            }

            $order->save();
            $tmp['id'] = (string)$order->id;

            $data[] = $tmp;
        }

        return $data;
    }

    private function checkout(array $items): Collection
    {
        $orderItems = new Collection();
        foreach ($items as $item)
            $orderItems->add(OrderItem::create($item['privateId'] ?? $item['id'], $item['price'], $item['quantity']));

        return $orderItems;
    }

    public function getStores(Request $request): array
    {
        if (!$city = $request->cookie('city', City::find(1)?->name))
            throw new OrderException('Не указан город!');

        $carts = $request->collect();
        if (!$carts->count())
            throw new OrderException('Нет товаров в корзине!');

        $stores = [];
        Offer::whereIn('product_id', $carts->keys())->whereCity($city)
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

    private function checkOrderId(int $orderId): void
    {
        if ($orderId > 2840 /* 4430 */) {
            $redisClient = Redis::connection('bot')->client();
            $redisClient->publish('bot:info', "Необходимо обновить id заказов! Id текущего заказа: {$orderId}");
        }
    }
}
