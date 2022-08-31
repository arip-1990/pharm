<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Payment::firstOrCreate([
            'slug_id' => '001'
        ], [
            'title' => 'Картой в приложении',
            'description' => 'Оплата картой visa или mastercard в приложении',
            'type' => Payment::TYPE_CARD,
        ]);
    }
}
