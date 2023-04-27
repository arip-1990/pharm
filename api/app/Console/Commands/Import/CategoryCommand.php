<?php

namespace App\Console\Commands\Import;

use App\Product\Entity\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(): int
    {
        $redis = Redis::connection('bot')->client();

        try {
            $data = $this->getData();
            foreach ($data->categories->category as $item) {
                $attr = $item->attributes();

                $category = Category::updateOrCreate(['id' => (int)$attr->id, 'name' => (string)$item]);
                if ((int)$attr->parentId) {
                    $category->parent_id = (int)$attr->parentId;
                    $category->save();
                }
            }
        }
        catch (\Exception $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'category',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }

        $redis->publish('bot:import', json_encode([
            'success' => true,
            'type' => 'category',
            'message' => 'Категории успешно обновлены: ' . $this->startTime->diff(Carbon::now())->format('%iм %sс')
        ], JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
