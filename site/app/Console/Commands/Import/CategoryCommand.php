<?php

namespace App\Console\Commands\Import;

use App\Entities\Category;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(): int
    {
        try {
            $data = $this->getData();
            Category::query()->delete();
            $categoryFields = [];
            $i = 0;
            foreach ($data->categories->category as $item) {
                $attr = $item->attributes();
                $slug = SlugService::createSlug(Category::class, 'slug', (string)$item);
                if (!$slug) continue;

                $dblCount = Category::query()->where('slug', 'similar to', "{$slug}(-[0-9]{1,2})?")->count();
                foreach ($categoryFields as $field) {
                    if (preg_match("/^{$slug}(-[0-9]{1,2})?$/", $field['slug']))
                        $dblCount++;
                }

                $categoryFields[] = [
                    'id' => (int)$attr->id,
                    'name' => (string)$item,
                    'slug' => $dblCount ? ($slug . '-' . ++$dblCount) : $slug,
                    'parent_id' => (int)$attr->parentId ?: null
                ];
                $i++;

                if ($i >= 1000) {
                    Category::query()->upsert($categoryFields, 'id', ['name', 'slug', 'parent_id']);
                    $categoryFields = [];
                    $i = 0;
                }
            }

            if ($i) Category::query()->upsert($categoryFields, 'id', ['name', 'slug', 'parent_id']);
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
