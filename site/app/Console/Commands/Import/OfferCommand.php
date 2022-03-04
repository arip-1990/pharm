<?php

namespace App\Console\Commands\Import;

use App\Entities\Product;
use App\Entities\Offer;

class OfferCommand extends Command
{
    protected $signature = 'import:offer {type=all}';
    protected $description = 'Import offers
                            {change : update offers}
                            {stock : import stock offers}';

    public function handle(): int
    {
        try {
            switch ($this->argument('type')) {
                case 'change':
                    $this->change();
                    break;
                case 'stock':
                    $this->stock();
                    break;
                default:
                    $this->all();
            }
        }
        catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff()->format('%iм %sс'));
        return 0;
    }

    private function change(): void
    {
        $data = $this->getData(4);
        $fields = [];
        $delFields = [];
        $i = 0;
        foreach ($data->stocks->stock as $item) {
            if (!$product = Product::query()->find((string)$item->code)) continue;
            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $storeId = (string)$item->store_uuid;
            $delFields[] = [$storeId, $product->id];
            $fields[] = [
                'store_id' => $storeId,
                'product_id' => $product->id,
                'price' => $price > 0 ? $price : 0,
                'quantity' => $quantity > 0 ? $quantity : 0
            ];
            $i++;

            if ($i >= 1000) {
                Offer::query()->whereInMultiple(['store_id', 'product_id'], $delFields)->delete();
                Offer::query()->upsert($fields, ['store_id', 'product_id']);
                $fields = [];
                $delFields = [];
                $i = 0;
            }
        }
        if ($i) {
            Offer::query()->whereInMultiple(['store_id', 'product_id'], $delFields)->delete();
            Offer::query()->upsert($fields, ['store_id', 'product_id']);
        }
    }

    private function all(): void
    {
        $data = $this->getData(3);
        Offer::query()->truncate();
        Product::query()->update(['status' => false]);
        $fields = [];
        $productIds = [];
        $i = 0;
        foreach ($data->stocks->stock as $item) {
            if (!$product = Product::query()->find((string)$item->code)) continue;

            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $fields[] = [
                'store_id' => (string)$item->store_uuid,
                'product_id' => $product->id,
                'price' => $price > 0 ? $price : 0,
                'quantity' => $quantity > 0 ? $quantity : 0
            ];
            $productIds[] = $product->id;
            $i++;

            if ($i >= 1000) {
                Offer::query()->upsert($fields, ['store_id', 'product_id']);
                Product::query()->whereIn('id', $productIds)->update(['status' => true]);
                $productIds = [];
                $fields = [];
                $i = 0;
            }
        }
        if ($i) {
            Offer::query()->upsert($fields, ['store_id', 'product_id']);
            Product::query()->whereIn('id', $productIds)->update(['status' => true]);
        }
    }

    private function stock(): void
    {
        $data = $this->getData(6);
        Offer::query()->where('store_id', config('data.stock'))->delete();
        $fields = [];
        $i = 0;
        foreach ($data->offers->offer as $item) {
            if (!$product = Product::query()->find((string)$item->uuid)) continue;
            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $fields[] = [
                'store_id' => (string)$item->store_uuid,
                'product_id' => $product->id,
                'price' => $price > 0 ? $price : 0,
                'quantity' => $quantity > 0 ? $quantity : 0
            ];
            $i++;

            if ($i >= 1000) {
                Offer::query()->upsert($fields, ['store_id', 'product_id']);
                $fields = [];
                $i = 0;
            }
        }
        if ($i) Offer::query()->upsert($fields, ['store_id', 'product_id']);
    }
}
