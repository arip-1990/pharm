<?php

namespace App\Product\UseCase;

use App\Product\Entity\Offer;
use App\Product\Entity\Product;
use App\Store\Entity\Store;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OfferService
{
    public function updateData(string $type = 'all'): void
    {
        $config = config('services.1c');
        try {
            $client = new Client([
                'base_uri' => $config['base_url'],
                'auth' => [$config['login'], $config['password']],
                'verify' => false
            ]);

            $fields = [];
            $delFields = [];
            $i = 0;

            if ($type === 'change') {
                $response = $client->get($config['urls'][4]);
                $xml = simplexml_load_string($response->getBody()->getContents());
                if ($xml === false)
                    throw new \DomainException('Ошибка парсинга xml');

                foreach ($xml->stocks->stock as $item) {
                    if (!$product = Product::find((string) $item->code) or str_starts_with($product->name, '*'))
                        continue;

                    $price = (float) $item->price;
                    $quantity = (int) $item->quantity;
                    $storeId = (string) $item->store_uuid;
                    $delFields[] = [$storeId, $product->id];
                    $fields[] = [
                        'store_id' => $storeId,
                        'product_id' => $product->id,
                        'price' => max($price, 0),
                        'quantity' => max($quantity, 0)
                    ];
                    $i++;

                    if ($i >= 1000) {
                        Offer::whereInMultiple(['store_id', 'product_id'], $delFields)->delete();
                        Offer::upsert($fields, ['store_id', 'product_id']);
                        $fields = [];
                        $delFields = [];
                        $i = 0;
                    }
                }
            }
            elseif ($type === 'stock') {
                $response = $client->get($config['urls'][6]);
                $xml = simplexml_load_string($response->getBody()->getContents());
                if ($xml === false)
                    throw new \DomainException('Ошибка парсинга xml');

                Offer::where('store_id', config('data.stock'))->delete();
                foreach ($xml->offers->offer as $item) {
                    if (!$product = Product::find((string) $item->uuid) or str_starts_with($product->name, '*'))
                        continue;

                    $price = (float) $item->price;
                    $quantity = (int) $item->quantity;
                    $fields[] = [
                        'store_id' => (string) $item->store_uuid,
                        'product_id' => $product->id,
                        'price' => max($price, 0),
                        'quantity' => max($quantity, 0)
                    ];
                    $i++;

                    if ($i >= 1000) {
                        Offer::upsert($fields, ['store_id', 'product_id']);
                        $fields = [];
                        $i = 0;
                    }
                }
            }
            else {
                $response = $client->get($config['urls'][3]);
                $xml = simplexml_load_string($response->getBody()->getContents());
                if ($xml === false)
                    throw new \DomainException('Ошибка парсинга xml');

                Offer::truncate();
                foreach ($xml->stocks->stock as $item) {
                    if (!$product = Product::find((string) $item->code) or str_starts_with($product->name, '*'))
                        continue;
                    if (!$store = Store::find((string) $item->store_uuid))
                        continue;

                    $price = (float) $item->price;
                    $quantity = (int) $item->quantity;
                    $fields[] = [
                        'store_id' => $store->id,
                        'product_id' => $product->id,
                        'price' => max($price, 0),
                        'quantity' => max($quantity, 0)
                    ];
                    $i++;

                    if ($i >= 1000) {
                        Offer::upsert($fields, ['store_id', 'product_id']);
                        $fields = [];
                        $i = 0;
                    }
                }
            }

            if ($i) {
                if (count($delFields)) Offer::whereInMultiple(['store_id', 'product_id'], $delFields)->delete();
                Offer::upsert($fields, ['store_id', 'product_id']);
            }


        } catch (\Exception | GuzzleException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
