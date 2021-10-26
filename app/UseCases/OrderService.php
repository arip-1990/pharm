<?php

namespace App\UseCases;

use App\Entities\Order;
use Illuminate\Http\Request;

class OrderService
{
//    public function __construct(private OrderRepository $orderRepository, private OfferRepository $offerRepository) {}
//
//    public function checkout(Request $request): Order
//    {
//        if ($request->delivery_type === Order::DELIVERY_TYPE_COURIER and $request->payment_type == Order::PAYMENT_TYPE_CASH)
//            throw new \DomainException('Не возможно оплатить наличными');
//
//        $offers = [];
//        $items = array_map(function (CartItem $item) use (&$offers) {
//            $offer = $item->getOffer();
//            $offer->checkout($item->getQuantity());
//            $offers[] = $offer;
//
//            return OrderItem::create(
//                $offer->product,
//                $item->getPrice(),
//                $item->getQuantity()
//            );
//        }, $cart->getItems());
//
//        $order = Order::create(
//            $offers[0]->store,
//            $items,
//            $cart->getCost()->getTotal(),
//            $request->payment_type
//        );
//
//        if ($request->delivery_type === Order::DELIVERY_TYPE_COURIER) {
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
//        if ($user = $this->getUser())
//            $order->addUser($user);
//
//        $this->transaction->wrap(function () use ($order, $offers) {
//            $this->order_repo->save($order);
//            foreach ($offers as $offer)
//                $this->offer_repo->save($offer);
//        });
//
//        foreach ($this->cart->getItems() as $item) {
//            try {
//                $newItem = $cart->getItem($item->getId());
//                $quantity = $item->getQuantity() - $newItem->getQuantity();
//                $this->cart->remove($item->getId());
//                if ($quantity > 0)
//                    $this->cart->add($item->changeQuantity($quantity));
//            }
//            catch (\DomainException $exception) {}
//        }
//        $cart->clear();
//
//        return $order;
//    }
//
//    public function paymentSberbank(Order $order, string $redirectUrl): string
//    {
//        $curl = curl_init();
//        $config = \Yii::$app->params['payments']['sberbank'];
//        $config = $config[$config['type']];
//        $url = $config['url'];
//        $username = $config['username'];
//        $password = $config['password'];
//
//        curl_setopt_array($curl, [
//            CURLOPT_URL => $url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => http_build_query([
//                'userName'      => $username,
//                'password'      => $password,
//                'orderNumber'   => $config['prefix_number'] . $order->id,
//                'amount'        => $order->getTotalCost() * 100,
//                'returnUrl'     => $redirectUrl,
//            ])
//        ]);
//
//        $response = curl_exec($curl);
//        curl_close($curl);
//
//        $response = json_decode($response, true);
//
//        if(isset($response['errorCode']))
//            throw new HttpException(500, 'Не удалось создать форму оплаты. ' . $response['errorMessage']);
//
//        $order->pay($response['orderId']);
//        $this->order_repo->save($order);
//        return $response['formUrl'];
//    }
//
//    private function getUser(): ?User
//    {
//        return \Yii::$app->user->identity ? User::findOne(\Yii::$app->user->id) : null;
//    }
}
