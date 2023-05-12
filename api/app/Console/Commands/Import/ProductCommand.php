<?php

namespace App\Console\Commands\Import;

use App\Product\Entity\{Product, Value};
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Redis;

class ProductCommand extends Command
{
    protected $signature = 'import:product';
    protected $description = 'Import products';

    public function handle(): int
    {
        $this->startTime = Carbon::now();
        try {
            $data = $this->getData();
            $productFields = [];
            $valueFields = [];
            $i = 0;
            foreach ($data->goods->good as $item) {
                $productFields[] = [
                    'id' => (string) $item->uuid,
                    'category_id' => (int) $item->category ?: null,
                    'name' => (string) $item->name,
                    'code' => (int) $item->code,
                    'recipe' => (string) $item->recipe === 'true' ? true : (Product::find((string) $item->uuid)?->recipe ?? false),
                    'marked' => (string) $item->is_marked === 'true',
                ];

                if ($vendor = (string) $item->vendor) {
                    $valueFields[] = [
                        'attribute_id' => 1,
                        'product_id' => (string) $item->uuid,
                        'value' => $vendor
                    ];
                }

                $i++;

                if ($i >= 1000) {
                    Product::upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe']);
                    Value::upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
                    $productFields = [];
                    $valueFields = [];
                    $i = 0;
                }
            }

            if ($i) {
                Product::upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe']);
                Value::upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
            }

            foreach (Product::whereNull('slug')->get() as $product) {
                $product->update(['slug' => SlugService::createSlug(Product::class, 'slug', $product->name)]);
            }

            $this->redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'product',
                'message' => 'Товары успешно обновлены: ' . $this->startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            $this->redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'product',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        } finally {
            $this->startTime = null;
        }

        return self::SUCCESS;
    }
}