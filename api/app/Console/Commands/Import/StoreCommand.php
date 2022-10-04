<?php

namespace App\Console\Commands\Import;

use App\Models\City;
use App\Models\Location;
use App\Models\Store;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Redis;

class StoreCommand extends Command
{
    protected $signature = 'import:store';
    protected $description = 'Import pharmacies';

    public function handle(): int
    {
        $client = Redis::client();
        try {
            $data = $this->getData(2);
            $fields = [];
            foreach ($data->pharmacies->pharmacy as $item) {
                $address = explode(',', $item->address);
                if (!$city = City::firstWhere('name', trim($address[0])))
                    continue;

                $street = trim(str_replace(['пр.', 'пр', 'ул.', 'ул'], '', $address[1]));
                $house = trim(str_replace(['д.', 'д'], '', $address[2]));
                $location = Location::firstOrCreate(['city_id' => $city->id, 'street' => $street, 'house' => $house]);
                if ($coordinates = $item->coordinates) $location->update(['coordinate' => [(float)$coordinates->lat, (float)$coordinates->lon]]);

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
                    'schedule' => json_encode($schedules, JSON_UNESCAPED_UNICODE),
                    'location_id' => $location->id
                ];
            }

            Store::upsert($fields, 'id', ['name', 'slug', 'phone', 'schedule']);
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

        $client->publish("bot:import", json_encode(['success' => true, 'message' => 'Аптеки успешно обновлены']));
        $this->info('Загрузка успешно завершена! ' . $this->startTime->diff()->format('%iм %sс'));
        return 0;
    }
}
