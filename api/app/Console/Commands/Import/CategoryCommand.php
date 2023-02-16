<?php

namespace App\Console\Commands\Import;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(): int
    {
        $connection = Queue::connection();
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
            $connection->pushRaw(json_encode([
                'type' => 'error',
                'data' => [
                    'file' => self::class,
                    'message' => $e->getMessage()
                ]
            ]), 'bot');

            $this->info($e->getMessage());

            return self::FAILURE;
        }

        $connection->pushRaw(json_encode([
            'type' => 'info',
            'message' => 'Категории успешно обновлены'
        ]), 'bot');
        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff(Carbon::now())->format('%iм %sс'));

        return self::SUCCESS;
    }
}
