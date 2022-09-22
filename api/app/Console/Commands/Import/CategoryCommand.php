<?php

namespace App\Console\Commands\Import;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(): int
    {
        $client = Redis::connection()->client();
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
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            $client->publish("bot:import", json_encode(['message' => $e->getMessage()]));
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
