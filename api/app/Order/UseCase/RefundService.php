<?php

namespace App\Order\UseCase;

use App\Order\Entity\Order;

class RefundService
{
    public function partlyRefund(Order $order, \SimpleXMLElement $products): void
    {
        $newItems = [];
        $refund = false;
        foreach ($order->items as $item) {
            foreach ($products as $product) {
                if ($product->code === $item->product->code and $product->price === $item->price) {
                    if ($item->quantity > $product->quantity) {
                        $refund = true;
                        $item->quantity = $product->quantity;
                    }

                    $newItems[] = $item;
                }
            }
        }

        if ($refund) {
            $order->items()->saveMany($newItems);
            $order->partlyRefund();
        }
    }

    public function fullRefund(Order $order): void
    {
        $order->delete();
        $order->fullRefund();
    }
}
