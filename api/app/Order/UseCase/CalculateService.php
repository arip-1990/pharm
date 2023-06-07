<?php

namespace App\Order\UseCase;

use App\Store\Entity\{City, Location, Store};
use Illuminate\Database\Eloquent\Builder;

class CalculateService
{
    public function handle(array $items, City $city, Store $store = null, bool $isMobile = false): array
    {
        if ($store) return $this->calc($items, $store, $city->isBookingAvailable());

        if ($isMobile) $storeIds = config('data.mobileStores')[$city->id];
        else $storeIds = Store::whereIn('location_id', Location::whereCity($city)->pluck('id'))->pluck('id');

        $store = Store::select('stores.*')->join('offers', 'stores.id', '=', 'offers.store_id')
            ->whereIn('stores.id', $storeIds)->whereIn('offers.product_id', array_column($items, 'privateId'))
            ->groupBy('stores.id')->orderByRaw('count(stores.id) desc')->first();

        return $this->calc($items, $store, $city->isBookingAvailable());
    }

    public function isPickupAvailable(array $items, Store $store): bool
    {
        return $store->offers()->where(function (Builder $query) use ($items) {
            foreach ($items as $item) {
                $query->orWhere('product_id', $item['privateId'] ?? $item['id'])
                    ->where('quantity', '>=', $item['quantity']);
            }
        })->count() >= count($items);
    }

    private function calc(array $items, Store $store, bool $isBooking = false): array
    {
        $data = ['items' => [], 'totalPrice' => 0];
        $offers = $store->offers()->where('quantity', '>', 0)
            ->whereIn('product_id', array_column($items, 'privateId') ?: array_column($items, 'id'))->get();
        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['id'];
            $error = null;
            $deliveries = ['2'];

            if (!$offer = $offers->firstWhere('product_id', $productId) or $item['quantity'] > $offer->quantity) {
                if ($isBooking) $deliveries = ['3'];
                elseif ($offer) $error['error'] = "Доступно всего {$offer->quantity} количество";
                else $error['unavailableDeliveryMessage'] = "Товара нет в наличии";
            }

            $data['items'][] = $this->generateItem($productId, $item['name'], $item['price'], $item['quantity'], $deliveries, $error);
            $data['totalPrice'] += $item['price'] * $item['quantity'];
        }

        return $data;
    }

    private function generateItem(string $id, string $name, float $price, int $quantity, array $deliveries = null, array $error = null): array
    {
        $data = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'discount' => 0,
            'subtotal' => $price * $quantity
        ];

        if ($deliveries) $data['deliveryGroups'] = $deliveries;
        if ($error) $data = [...$data, ...$error];

        return $data;
    }
}
