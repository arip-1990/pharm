<?php

namespace App\Console\Commands\Import;

use App\Entities\Product;
use App\Entities\Value;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class ProductCommand extends Command
{
    protected $signature = 'import:product {--update}';
    protected $description = 'Import products
                            {--update : update products}';

    public function handle(): int
    {
        try {
            $data = $this->getData();
            if ($this->option('update'))
                $this->update($data->goods->good);
            else
                $this->product($data->goods->good);
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }

    private function product(\SimpleXMLElement $products): void
    {
        $productFields = [];
        $valueFields = [];
        $i = 0;
        foreach ($products as $item) {
            $productFields[] = [
                'id' => (string)$item->uuid,
                'category_id' => (int)$item->category ?: null,
                'name' => (string)$item->name,
                'slug' => SlugService::createSlug(Product::class, 'slug', (string)$item->name),
                'code' => (int)$item->code
            ];
            $i++;

            if ($i >= 1000) {
                Product::query()->upsert($productFields, 'id', ['category_id', 'name', 'slug', 'code']);
                $productFields = [];
                $i = 0;
            }
        }
        if ($i) {
            Product::query()->upsert($productFields, 'id', ['category_id', 'name', 'slug', 'code']);
            $i = 0;
        }

        foreach ($products as $item) {
            if (!$vendor = (string)$item->vendor or !$product = Product::query()->find((string)$item->uuid))
                continue;

            $valueFields[] = [
                'attribute_id' => 1,
                'product_id' => (string)$product->id,
                'value' => $vendor
            ];
            $i++;

            if ($i >= 1000) {
                Value::query()->upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
                $valueFields = [];
                $i = 0;
            }
        }
        if ($i) Value::query()->upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
    }

    private function update(\SimpleXMLElement $products): void
    {
        foreach ($products as $item) {
            if (!$product = Product::query()->find((string)$item->uuid))
                continue;

            $product->update([
                'category_id' => (int)$item->category ?: null,
                'name' => (string)$item->name,
                'code' => (int)$item->code
            ]);
        }
    }
}
