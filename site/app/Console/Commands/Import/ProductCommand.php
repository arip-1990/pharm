<?php

namespace App\Console\Commands\Import;

use App\Entities\Product;
use App\Entities\Value;
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
                $slug = SlugService::createSlug(Product::class, 'slug', (string)$item->name);
                if (!$slug) continue;

                $dblCount = Product::query()->where('slug', 'similar to', "{$slug}(-[0-9]{1,2})?")->count();
                foreach ($productFields as $field) {
                    if (preg_match("/^{$slug}(-[0-9]{1,2})?$/", $field['slug']))
                        $dblCount++;
                }

                $productFields[] = [
                    'id' => (string)$item->uuid,
                    'category_id' => (int)$item->category ?: null,
                    'name' => (string)$item->name,
                    'slug' => $dblCount ? ($slug . '-' . ++$dblCount) : $slug,
                    'code' => (int)$item->code
                ];
                $i++;

                if ($i >= 1000) {
                    Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'slug']);
                    $productFields = [];
                    $i = 0;
                }
            }

            if ($i) {
                Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'slug']);
                $i = 0;
            }

            foreach ($data->goods->good as $item) {
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
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
