<?php

namespace App\Console\Commands\Import;

use App\Models\Product;
use App\Models\Value;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class ProductCommand extends Command
{
    protected $signature = 'import:product';
    protected $description = 'Import products';

    public function handle(): int
    {
        try {
            $data = $this->getData();
            $productFields = [];
            $valueFields = [];
            $i = 0;
            foreach ($data->goods->good as $item) {
                $productFields[] = [
                    'id' => (string)$item->uuid,
                    'category_id' => (int)$item->category ?: null,
                    'name' => (string)$item->name,
                    'code' => (int)$item->code,
                    'recipe' => (string)$item->recipe === 'true' ? true : (Product::query()->find((string)$item->uuid)?->recipe ?? false),
                    'marked' => (string)$item->is_marked === 'true',
                    'sale' => (string)$item->sale === 'true'
                ];

                if ($vendor = (string)$item->vendor) {
                    $valueFields[] = [
                        'attribute_id' => 1,
                        'product_id' => (string)$item->uuid,
                        'value' => $vendor
                    ];
                }

                $i++;

                if ($i >= 1000) {
                    Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe', 'sale']);
                    Value::query()->upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
                    $productFields = [];
                    $valueFields = [];
                    $i = 0;
                }

                $j++;
            }

            if ($i) {
                Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe', 'sale']);
                Value::query()->upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
            }

            foreach (Product::query()->whereNull('slug')->get() as $product) {
                $product->update(['slug' => SlugService::createSlug(Product::class, 'slug', $product->name)]);
            }
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
