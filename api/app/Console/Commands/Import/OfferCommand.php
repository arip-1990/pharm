<?php

namespace App\Console\Commands\Import;

use App\Store\Entity\Store;
use Carbon\Carbon;
use App\Product\Entity\{Offer, Product};

class OfferCommand extends Command
{
    protected $signature = 'import:offer {type=all}';
    protected $description = 'Import offers
                            {change : update offers}
                            {stock : import stock offers}';

    public function handle(): int
    {
        $type = $this->argument('type');
        $this->startTime = Carbon::now();
        try {
            switch ($type) {
                case 'change':
                    $this->change();
                    break;
                case 'stock':
                    $this->stock();
                    break;
                default:
                    $this->all();
                    $this->redis->publish('bot:import', json_encode([
                        'success' => true,
                        'type' => 'offer:' . $type,
                        'message' => 'Остатки успешно обновлены: ' . $this->startTime->diff(Carbon::now())->format('%iм %sс')
                    ], JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            $this->redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'offer:' . $type,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        } finally {
            $this->startTime = null;
        }

        return self::SUCCESS;
    }

    private function change(): void
    {
        $data = $this->getData(4);
        $fields = [];
        $delFields = [];
        $i = 0;
        foreach ($data->stocks->stock as $item) {
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
        if ($i) {
            Offer::whereInMultiple(['store_id', 'product_id'], $delFields)->delete();
            Offer::upsert($fields, ['store_id', 'product_id']);
        }
    }

    private function all(): void
    {
        $data = $this->getData(3);
        Offer::truncate();
        $fields = [];
        $i = 0;
        foreach ($data->stocks->stock as $item) {
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

        if ($i) {
            Offer::upsert($fields, ['store_id', 'product_id']);
        }
    }

    private function stock(): void
    {
        $data = $this->getData(6);
        Offer::where('store_id', config('data.stock'))->delete();
        $fields = [];
        $i = 0;
        foreach ($data->offers->offer as $item) {
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
        if ($i)
            Offer::upsert($fields, ['store_id', 'product_id']);
    }
}
