<?php

namespace Database\Seeders;

use App\Models\TimeInterval;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimeIntervalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'time_start' => Carbon::now()->setTimeFromTimeString('10:00'),
            'time_end' => Carbon::now()->setTimeFromTimeString('20:00')
        ];
        TimeInterval::firstOrCreate($data);
    }
}
