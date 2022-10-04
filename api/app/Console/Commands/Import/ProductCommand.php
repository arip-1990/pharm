<?php

namespace App\Console\Commands\Import;

use App\Models\Product;
use App\Models\Value;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Redis;

class ProductCommand extends Command
{
    protected $signature = 'import:product';
    protected $description = 'Import products';

    public function handle(): int
    {
        $client = Redis::client();
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
                    'recipe' => (string)$item->recipe === 'true' ? true : (Product::find((string)$item->uuid)?->recipe ?? false),
                    'marked' => (string)$item->is_marked === 'true',
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
        }
        catch (\DomainException $e) {
            $this->error($e->getMessage());
            $client->publish("bot:import", json_encode([
                'success' => false,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]));
            return 1;
        }

        $client->publish("bot:import", json_encode(['success' => true, 'message' => 'Товары успешно обновлены']));
        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
