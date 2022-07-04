<?php

namespace App\Console\Commands\Import;

use App\Models\Store;
use Cviebrock\EloquentSluggable\Services\SlugService;

class StoreCommand extends Command
{
    protected $signature = 'import:store';
    protected $description = 'Import pharmacies';

    public function handle(): int
    {
        try {
            $data = $this->getData(2);
            $fields = [];
            foreach ($data->pharmacies->pharmacy as $item) {
                $cord = $item->coordinates;
                $schedules =  [];
                foreach ($item->schedules->schedule as $schedule) {
                    $schedules[] = [
                        'open'    => (string)$schedule->open_time,
                        'close'   => (string)$schedule->close_time
                    ];
                }

                $fields[] = [
                    'id' => (string)$item->uuid,
                    'name' => (string)$item->title,
                    'slug' => SlugService::createSlug(Store::class, 'slug', (string)$item->title),
                    'phone' => (string)$item->phone ?: null,
                    'address' => (string)$item->address ?: null,
                    'lon' => (string)$cord->lon ?: null,
                    'lat' => (string)$cord->lat ?: null,
                    'schedule' => json_encode($schedules, JSON_UNESCAPED_UNICODE)
                ];
            }

            Store::query()->upsert($fields, 'id', ['name', 'slug', 'phone', 'lon', 'lat', 'schedule']);
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff()->format('%iм %sс'));
        return 0;
    }
}
