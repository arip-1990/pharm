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
                $slug = SlugService::createSlug(Product::class, 'slug', (string)$item->name);
                if (!$slug) continue;

                foreach ($productFields as $field) {
                    if ($field['slug'] === $slug) {
                        if (preg_match("/^.*? (\d+)$/", (string)$item->name, $matches)) {
                            $slug = preg_replace("/^(.*?-{$matches[1][0]})-\d{1,2}$/", "$1", $slug);
                            if (preg_match("/^.*?-{$matches[1][0]}-(\d{1,2})$/", $slug, $matches))
                                $slug .= ('-' . ($matches[1][0] + 1));
                            else
                                $slug .= '-2';
                        }
                        else {
                            $slug = preg_replace("/^(.*?)(-\d{1,2})?$/", "$1", $slug);
                            if (preg_match("/^.*?-(\d{1,2})$/", $slug, $matches))
                                $slug .= ('-' . ($matches[1][0] + 1));
                            else
                                $slug .= '-2';
                        }
                    }
                }

                $productFields[] = [
                    'id' => (string)$item->uuid,
                    'category_id' => (int)$item->category ?: null,
                    'name' => (string)$item->name,
                    'slug' => $slug,
                    'code' => (int)$item->code,
                    'recipe' => (string)$item->recipe === 'true' ? true : (Product::query()->find((string)$item->uuid)?->recipe ?? false),
                    'marked' => (string)$item->is_marked === 'true',
                    'sale' => (string)$item->sale === 'true'
                ];
                $i++;

                if ($i >= 1000) {
                    Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe', 'sale']);
                    $productFields = [];
                    $i = 0;
                }
            }

            if ($i) {
                Product::query()->upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe', 'sale']);
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
