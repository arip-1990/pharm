<?php

namespace App\Console\Commands\Import;

use App\Models\Product;
use App\Models\Offer;

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
            if (!$product = Product::query()->find((string)$item->code) or str_starts_with($product->name, '*')) continue;

            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $storeId = (string)$item->store_uuid;
            $delFields[] = [$storeId, $product->id];
            $fields[] = [
                'store_id' => $storeId,
                'product_id' => $product->id,
                'price' => max($price, 0),
                'quantity' => max($quantity, 0)
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
        $fields = [];
        $i = 0;
        foreach ($data->stocks->stock as $item) {
            if (!$product = Product::query()->find((string)$item->code) or str_starts_with($product->name, '*')) continue;

            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $fields[] = [
                'store_id' => (string)$item->store_uuid,
                'product_id' => $product->id,
                'price' => max($price, 0),
                'quantity' => max($quantity, 0)
            ];
            $i++;

            if ($i >= 1000) {
                Offer::query()->upsert($fields, ['store_id', 'product_id']);
                $fields = [];
                $i = 0;
            }
        }

        if ($i) {
            Offer::query()->upsert($fields, ['store_id', 'product_id']);
        }
    }

    private function stock(): void
    {
        $data = $this->getData(6);
        Offer::query()->where('store_id', config('data.stock'))->delete();
        $fields = [];
        $i = 0;
        foreach ($data->offers->offer as $item) {
            if (!$product = Product::query()->find((string)$item->code) or str_starts_with($product->name, '*')) continue;
            $price = (float)$item->price;
            $quantity = (int)$item->quantity;
            $fields[] = [
                'store_id' => (string)$item->store_uuid,
                'product_id' => $product->id,
                'price' => max($price, 0),
                'quantity' => max($quantity, 0)
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
