<?php

namespace Database\Seeders;

use App\Product\Entity\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            '30' => [
                'name' => '30% бонус',
                'description' => 'Вернем баллами 30%',
                'percent' => 30.00,
                'active' => true
            ],
            'manager' => [
                'name' => '50% бонус',
                'description' => 'Вернем баллами 50%',
                'percent' => 50.00,
                'active' => true
            ],
        ];

        foreach ($data as $item) Discount::firstOrCreate($item);
    }
}
