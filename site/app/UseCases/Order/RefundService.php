<?php

namespace App\UseCases\Order;

use App\Entities\Archive;
use App\Entities\Order;

class RefundService
{
    public function partlyRefund(Order $order, \SimpleXMLElement $products): void
    {
        $newItems = [];
        $archives = [];
        $refund = false;
        foreach ($order->items as $item) {
            $archives[] = Archive::create($order, $item->product, $item->price, $item->quantity);
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
            foreach ($archives as $archive)
                $archive->save();

            $order->items = $newItems;
            $order->partlyRefund();
        }
    }

    public function fullRefund(Order $order): void
    {
        foreach ($order->items as $item) {
            $archive = new Archive([
                'order_id' => $order->id,
                'code' => $item->product->code,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity
            ]);
            $archive->save();
        }

        $order->items = [];
        $order->fullRefund();
    }
}
