<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'makhachkala' => [
                'name' => 'Махачкала',
                'prefix' => 'г',
                'type' => City::TYPE_CITY
            ],
            'kaspiysk' => [
                'name' => 'Каспийск',
                'prefix' => 'г',
                'type' => City::TYPE_CITY
            ],
            'izberbash' => [
                'name' => 'Избербаш',
                'prefix' => 'г',
                'type' => City::TYPE_CITY
            ],
            'khasavyurt' => [
                'name' => 'Хасавюрт',
                'prefix' => 'г',
                'type' => City::TYPE_CITY
            ],
            'babayurt' => [
                'name' => 'Бабаюрт',
                'prefix' => 'с',
                'type' => City::TYPE_VILLAGE
            ],
            'novocayakent' => [
                'name' => 'Новокаякент',
                'prefix' => 'с',
                'type' => City::TYPE_VILLAGE
            ],
            'botlich' => [
                'name' => 'Ботлих',
                'prefix' => 'с',
                'type' => City::TYPE_VILLAGE
            ],
            'semender' => [
                'name' => 'Семендер',
                'prefix' => 'пос',
                'type' => City::TYPE_TOWNSHIP,
                'parent_id' => 1
            ],
            'karaman_7' => [
                'name' => 'Караман 7',
                'prefix' => 'мкр',
                'type' => City::TYPE_MICRO_DISTRICT,
                'parent_id' => 1
            ],
            'leninkent' => [
                'name' => 'Ленинкент',
                'prefix' => 'пос',
                'type' => City::TYPE_TOWNSHIP,
                'parent_id' => 1
            ],
        ];

        foreach ($data as $item) City::firstOrCreate($item);
    }
}
