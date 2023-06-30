<?php

namespace App\Store\UseCase;

use App\Store\Entity\City;
use App\Store\Entity\Location;
use App\Store\Entity\Store;
use Cviebrock\EloquentSluggable\Services\SlugService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StoreService
{
    public function updateData(): void
    {
        $config = config('services.1c');
        try {
            $client = new Client([
                'base_uri' => $config['base_url'],
                'auth' => [$config['login'], $config['password']],
                'verify' => false
            ]);

            $response = $client->get($config['urls'][2]);
            $xml = simplexml_load_string($response->getBody()->getContents());
            if ($xml === false)
                throw new \DomainException('Ошибка парсинга xml');

            $fields = [];
            foreach ($xml->pharmacies->pharmacy as $item) {
                $address = explode(',', $item->address);
                if (!$city = City::firstWhere('name', trim($address[0])))
                    continue;

                $street = trim(str_replace(['пр.', 'пр ', 'ул.', 'ул '], '', $address[1]));
                $house = trim(str_replace(['д.', 'д '], '', $address[2]));
                $location = Location::firstOrCreate(['city_id' => $city->id, 'street' => $street, 'house' => $house]);
                if ($coordinates = $item->coordinates)
                    $location->update(['coordinate' => [(float) $coordinates->lat, (float) $coordinates->lon]]);

                $schedules = [];
                foreach ($item->schedules->schedule as $schedule) {
                    $schedules[] = [
                        'open' => (string) $schedule->open_time,
                        'close' => (string) $schedule->close_time
                    ];
                }

                $fields[] = [
                    'id' => (string) $item->uuid,
                    'name' => (string) $item->title,
                    'slug' => SlugService::createSlug(Store::class, 'slug', (string) $item->title),
                    'phone' => trim((string) $item->phone, '+') ?: null,
                    'schedule' => json_encode($schedules, JSON_UNESCAPED_UNICODE),
                    'location_id' => $location->id
                ];
            }

            Store::upsert($fields, 'id', ['name', 'slug', 'phone', 'schedule']);
        } catch (\Exception | GuzzleException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
