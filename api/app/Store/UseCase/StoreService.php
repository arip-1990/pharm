<?php

namespace App\Store\UseCase;

use App\Helper;
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
                $type = null;
                $prefix = null;
                $address = explode(',', $item->address);
                if (count($address) === 4) {
                    $city = Helper::trimPrefixCity($address[1]);
                    if (str_contains(mb_strtolower($address[2]), 'пр')) {
                        $type = Location::TYPE_AVENUE;
                        $prefix = 'пр';
                    }
                    elseif (str_contains(mb_strtolower($address[2]), 'ул')) {
                        $type = Location::TYPE_STREET;
                        $prefix = 'ул';
                    }

                    $street = Helper::trimPrefixStreet($address[2]);
                    $house = Helper::trimPrefixStreet($address[3]);
                }
                else {
                    $city = Helper::trimPrefixCity($address[0]);
                    if (str_contains(mb_strtolower($address[1]), 'пр')) {
                        $type = Location::TYPE_AVENUE;
                        $prefix = 'пр';
                    }
                    elseif (str_contains(mb_strtolower($address[1]), 'ул')) {
                        $type = Location::TYPE_STREET;
                        $prefix = 'ул';
                    }

                    $street = Helper::trimPrefixStreet($address[1]);
                    $house = Helper::trimPrefixStreet($address[2]);
                }

                if (!$city = City::firstWhere('name', $city))
                    continue;

                $street = str_replace(['Петра 1', 'Ахмет-хана'], ['Петра I', 'Амет-Хана'], $street);
                $house = preg_replace('/\(.+\)/', '', $house);
                if (!$location = Location::where('city_id', $city->id)->where('street', $street)->where('house', $house)->first())
                    $location = Location::create([
                        'type' => $type,
                        'prefix' => $prefix,
                        'city_id' => $city->id,
                        'street' => $street,
                        'house' => $house
                    ]);

                if ($coordinates = $item->coordinates and (float)$coordinates->lat)
                    $location->update(['coordinate' => [(float)$coordinates->lat, (float)$coordinates->lon]]);

                $schedules = [];
                foreach ($item->schedules->schedule as $schedule) {
                    $schedules[] = [
                        'open' => (string)$schedule->open_time,
                        'close' => (string)$schedule->close_time
                    ];
                }

                $fields[] = [
                    'id' => (string)$item->uuid,
                    'name' => (string)$item->title,
                    'slug' => SlugService::createSlug(Store::class, 'slug', (string)$item->title),
                    'phone' => ltrim((string)$item->phone, '+') ?: null,
                    'schedule' => json_encode($schedules, JSON_UNESCAPED_UNICODE),
                    'location_id' => $location->id,
                    'company_id' => str_contains(mb_strtolower((string)$item->title), 'дф') ? 2 : 1
                ];
            }

            Store::upsert($fields, 'id', ['name', 'slug', 'phone', 'schedule', 'company_id']);
        } catch (\Exception | GuzzleException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
