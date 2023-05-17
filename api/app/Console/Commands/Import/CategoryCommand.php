<?php

namespace App\Console\Commands\Import;

use App\Product\Entity\Category;
use Carbon\Carbon;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(): int
    {
        $this->startTime = Carbon::now();
        try {
            $data = $this->getData();
            foreach ($data->categories->category as $item) {
                $attr = $item->attributes();

                $category = Category::updateOrCreate(['id' => (int) $attr->id, 'name' => (string) $item]);
                if ((int) $attr->parentId) {
                    $category->parent_id = (int) $attr->parentId;
                    $category->save();
                }
            }

            $this->redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'category',
                'message' => 'Категории успешно обновлены: ' . $this->startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            $this->redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'category',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        } finally {
            $this->startTime = null;
        }

        return self::SUCCESS;
    }
}
