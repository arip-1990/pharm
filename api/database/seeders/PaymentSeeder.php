<?php

namespace Database\Seeders;

use App\Order\Entity\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'card' => [
                'title' => 'Картой в приложении',
                'description' => 'Оплата картой visa или mastercard в приложении',
                'type' => Payment::TYPE_CARD,
            ],
            'cash' => [
                'title' => 'Наличными в аптеке',
                'description' => 'Оплата наличными при получении',
                'type' => Payment::TYPE_CASH,
            ]
        ];

        foreach ($data as $item) Payment::firstOrCreate($item);
    }
}
