<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'regular' => [
                'title' => 'Доставка курьером',
                'description' => 'Доставка курьерской службой по городу',
                'type' => Delivery::TYPE_DELIVERY,
                'price' => 200,
                'min' => 0,
                'max' => 0,
            ],
            'pickup' => [
                'title' => 'Самовывоз из магазина',
                'description' => 'Самовывоз заказа из магазина в день заказа',
                'type' => Delivery::TYPE_PICKUP,
                'price' => 0,
                'min' => 0,
                'max' => 0,
            ]
        ];

        foreach ($data as $item) Delivery::firstOrCreate($item);
    }
}
