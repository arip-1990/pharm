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

    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

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

//    public function getStores(Request $request): array
//    {
//        if (!$city = $request->cookie('city', City::find(1)?->name))
//            throw new OrderException('Не указан город!');
//
//        $carts = $request->collect();
//        if (!$carts->count())
//            throw new OrderException('Нет товаров в корзине!');
//
//        $validComboProducts = [];
//
//        foreach ($carts as $item) {
//            if (!is_null($item['combo'])) {
//                $comboKey = $item['combo']; // оставляем строку как есть: "["11"]"
//                $validComboProducts[$comboKey][] = $item['prod_id'];
//            }
//        }
//
//        //dd($carts, $validComboProduct);
//
//        $stores = [];
//
//        Offer::whereIn('product_id', $carts->keys())->whereCity($city)
//            ->each(function (Offer $offer) use ($carts, &$stores, $validComboProducts) {
//                $cartQuantity = (int)$carts[$offer->product_id];
//                $stores[$offer->store_id]['store'] = new StoreResource($offer->store);
//                //$discountStorePrice = $offer->product->getDiscountPrice($offer->price);
//                $discountStorePrice = $this->discountService->getDiscountStore($validComboProducts, $offer->store_id, $offer->price, $offer->product, min($cartQuantity, $offer->quantity));
//                $stores[$offer->store_id]['products'][] = [
//                    'price' => $offer->price,
//                    'quantity' => min($cartQuantity, $offer->quantity),
//                    'product' => new ProductResource($offer->product),
//                    'discountStorePrice' => $discountStorePrice
//                ];
//            });
//
//        usort($stores, function ($a, $b) {
//            $res = count($b['products']) - count($a['products']);
//            if ($res) return $res;
//            else {
//                $price_a = 0;
//                $price_b = 0;
//                $quantity_a = 0;
//                $quantity_b = 0;
//                for ($i = 0; $i < count($a['products']); $i++) {
//                    $quantity_a = $a['products'][$i]['quantity'];
//                    $quantity_b = $b['products'][$i]['quantity'];
//                    $price_a += $quantity_a * $a['products'][$i]['price'];
//                    $price_b += $quantity_b * $b['products'][$i]['price'];
//                }
//                $res = $quantity_b - $quantity_a;
//                return $res ?: $price_a - $price_b;
//            }
//        });
//
//        $res = $this->discountService->uniqueValidCombo();
//
//        foreach ($res as $r) {
//            //dump($r);
//            foreach ($stores as &$store) {
//                if ($store['store']['id'] == $r['store_id']) {
//                    $minCount = min($r['products'][0]['quantity'], $r['products'][1]['quantity']);
//
//                    $checker = [$r['products'][0]['id'], $r['products'][1]['id']];
//
//                    foreach ($store['products'] as &$products) {
//                        if (in_array($products['product']['id'], $checker)) {
//                            $products['discountStorePrice'] -= $products['price'] * $r['percent'] /100;
//                            if ($minCount > 1) {
//                                for ($i = 1; $i < $minCount; $i++) {
//                                    $products['discountStorePrice'] -= $products['discountStorePrice'] * $r['percent'] /100;
//                                }
//                            }
//                        }
//                        //dump($products);
//                    }
//                    break;
//                }
//            }
//        }
//
////        dd($res, $stores);
////
//////        foreach ($stores as $store) {
//////
//////        }
//
//
//        return $stores;
//    }
    public function getStores(Request $request): array
    {
        // 1️⃣ Проверка города
        $city = $request->cookie('city', City::find(1)?->name);
        if (!$city) {
            throw new OrderException('Не указан город!');
        }

        // 2️⃣ Проверка корзины
        $carts = collect($request->all());
        if ($carts->isEmpty()) {
            throw new OrderException('Нет товаров в корзине!');
        }

        // 3️⃣ Подготовка combo-продуктов
        $validComboProducts = [];
        foreach ($carts as $item) {
            if (!empty($item['combo'])) {
                $comboKey = (string)$item['combo']; // сохраняем строковое значение
                $validComboProducts[$comboKey][] = $item['prod_id'];
            }
        }

        // 4️⃣ Получаем ID товаров
        $productIds = $carts->pluck('prod_id')->all();

        // 5️⃣ Формируем список магазинов
        $stores = [];
        $offers = Offer::whereIn('product_id', $productIds)
            ->whereCity($city)
            ->with(['store', 'product']) // чтобы избежать N+1 запросов
            ->get();

        foreach ($offers as $offer) {
            $cartItem = $carts->firstWhere('prod_id', $offer->product_id);
            $cartQuantity = (int) ($cartItem['quantity'] ?? 1);
            $availableQuantity = min($cartQuantity, $offer->quantity);

            $discountStorePrice = $this->discountService->getDiscountStore(
                $validComboProducts,
                $offer->store_id,
                $offer->price,
                $offer->product,
                $availableQuantity
            );

            $storeId = $offer->store_id;
            if (!isset($stores[$storeId])) {
                $stores[$storeId]['store'] = new StoreResource($offer->store);
                $stores[$storeId]['products'] = [];
            }

            $stores[$storeId]['products'][] = [
                'price' => $offer->price,
                'quantity' => $availableQuantity,
                'product' => new ProductResource($offer->product),
                'discountStorePrice' => $discountStorePrice,
            ];
        }

        // 6️⃣ Сортируем магазины
        $stores = array_values($stores); // сбрасываем ключи

        usort($stores, function ($a, $b) {
            $countDiff = count($b['products']) - count($a['products']);
            if ($countDiff !== 0) return $countDiff;

            $sumA = collect($a['products'])->sum(fn($p) => $p['price'] * $p['quantity']);
            $sumB = collect($b['products'])->sum(fn($p) => $p['price'] * $p['quantity']);

            // сначала по количеству (desc), потом по цене (asc)
            return $sumA <=> $sumB;
        });

        // 7️⃣ Применяем скидки для уникальных combo
        $combos = $this->discountService->uniqueValidCombo();
        foreach ($combos as $combo) {
            $storeId = $combo['store_id'] ?? null;
            if (!$storeId || !isset($stores)) continue;

            foreach ($stores as &$store) {
                if ($store['store']['id'] != $storeId) continue;

                if (count($combo['products']) < 2) continue;

                $ids = array_column($combo['products'], 'id');
                $quantities = array_column($combo['products'], 'quantity');
                $minCount = min($quantities);

                foreach ($store['products'] as &$product) {
                    if (in_array($product['product']['id'], $ids)) {
                        $discount = $product['price'] * $combo['percent'] / 100 * $minCount;
                        $product['discountStorePrice'] -= $discount;
                    }
                }
            }
        }

        return $stores;
    }
    private function checkOrderId(int $orderId): void
    {
        if ($orderId > 3980 /* 4430 */) {
            $redisClient = Redis::connection('bot')->client();
            $redisClient->publish('bot:info', "Необходимо обновить идентификация заказов!\nНомер текущего заказа: {$orderId}\nНомер существующего заказа: 3988");
        }
    }
}
