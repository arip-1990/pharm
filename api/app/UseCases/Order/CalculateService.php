<?php

namespace App\UseCases\Order;

use App\Models\Offer;

class CalculateService
{
    public function handle(array $items, string $storeId = null): array
    {
        $data = ['totalPrice' => 0, 'items' => []];

        foreach ($items as $item) {
            $tmp = $this->calc($item, $storeId);
            $data['items'][] = $tmp;
            $data['totalPrice'] += $tmp['subtotal'];
        }

        return $data;
    }

    private function calc(array $item, string $storeId = null): array
    {
        $query = Offer::where('quantity', '>=', $item['quantity'])
            ->where('product_id', $item['privateId'])->orderBy('price');

        if ($storeId) $query->where('store_id', $storeId);

        if (!$offer = $query->first()) {
            return [
                'id' => $item['privateId'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'],
                'subtotal' => $item['subtotal'],
                'error' => 'Данный товар нет в наличии'
            ];
        }

        return [
            'id' => $offer->product_id,
            'price' => $offer->price,
            'quantity' => $item['quantity'],
            'discount' => 0,
            'subtotal' => $offer->price * $item['quantity']
        ];
    }
}
