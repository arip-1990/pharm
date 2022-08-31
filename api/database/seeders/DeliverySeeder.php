<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'regular' => [
                'slug_id' => 'regular',
                'title' => 'Доставка курьером',
                'description' => 'Доставка курьерской службой по городу на следующий день',
                'type' => DeliveryType::DELIVERY,
                'price' => 350,
                'min' => 0,
                'max' => 0,
            ],
            'pickup' => [
                'slug_id' => 'pickup',
                'title' => 'Самовывоз из магазина',
                'description' => 'Самовывоз заказа из магазина в день заказа',
                'type' => DeliveryType::PICKUP,
                'price' => 0,
                'min' => 0,
                'max' => 0,
            ]
        ];

        foreach ($data as $item) {
            // dd($item['slug_id']);
            DeliveryType::firstOrCreate(
                [
                'slug_id' => $item['slug_id']
            ],
            $item);
        }
    }
}
