<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TimeDay;

class TimeDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            ['day_id' => 2, 'time_id' => 1],
            ['day_id' => 2, 'time_id' => 2],
            ['day_id' => 2, 'time_id' => 3],
            ['day_id' => 2, 'time_id' => 4],
            ['day_id' => 2, 'time_id' => 5],
            ['day_id' => 2, 'time_id' => 6],
            ['day_id' => 2, 'time_id' => 7],
            ['day_id' => 2, 'time_id' => 8],
            ['day_id' => 2, 'time_id' => 9],
            ['day_id' => 2, 'time_id' => 10],
            ['day_id' => 2, 'time_id' => 11],
            ['day_id' => 2, 'time_id' => 12],
            ['day_id' => 2, 'time_id' => 13],
            ['day_id' => 2, 'time_id' => 14],
            ['day_id' => 2, 'time_id' => 15],
            ['day_id' => 2, 'time_id' => 16],
            ['day_id' => 2, 'time_id' => 17],

            ['day_id' => 3, 'time_id' => 1],
            ['day_id' => 3, 'time_id' => 2],
            ['day_id' => 3, 'time_id' => 3],
            ['day_id' => 3, 'time_id' => 4],
            ['day_id' => 3, 'time_id' => 5],
            ['day_id' => 3, 'time_id' => 6],
            ['day_id' => 3, 'time_id' => 7],
            ['day_id' => 3, 'time_id' => 8],
            ['day_id' => 3, 'time_id' => 9],
            ['day_id' => 3, 'time_id' => 10],
            ['day_id' => 3, 'time_id' => 11],
            ['day_id' => 3, 'time_id' => 12],
            ['day_id' => 3, 'time_id' => 13],
            ['day_id' => 3, 'time_id' => 14],
            ['day_id' => 3, 'time_id' => 15],
            ['day_id' => 3, 'time_id' => 16],
            ['day_id' => 3, 'time_id' => 17],

            ['day_id' => 4, 'time_id' => 1],
            ['day_id' => 4, 'time_id' => 2],
            ['day_id' => 4, 'time_id' => 3],
            ['day_id' => 4, 'time_id' => 4],
            ['day_id' => 4, 'time_id' => 5],
            ['day_id' => 4, 'time_id' => 6],
            ['day_id' => 4, 'time_id' => 7],
            ['day_id' => 4, 'time_id' => 8],
            ['day_id' => 4, 'time_id' => 9],
            ['day_id' => 4, 'time_id' => 10],
            ['day_id' => 4, 'time_id' => 11],
            ['day_id' => 4, 'time_id' => 12],
            ['day_id' => 4, 'time_id' => 13],
            ['day_id' => 4, 'time_id' => 14],
            ['day_id' => 4, 'time_id' => 15],
            ['day_id' => 4, 'time_id' => 16],
            ['day_id' => 4, 'time_id' => 17],

            ['day_id' => 5, 'time_id' => 1],
            ['day_id' => 5, 'time_id' => 2],
            ['day_id' => 5, 'time_id' => 3],
            ['day_id' => 5, 'time_id' => 4],
            ['day_id' => 5, 'time_id' => 5],
            ['day_id' => 5, 'time_id' => 6],
            ['day_id' => 5, 'time_id' => 7],
            ['day_id' => 5, 'time_id' => 8],
            ['day_id' => 5, 'time_id' => 9],
            ['day_id' => 5, 'time_id' => 10],
            ['day_id' => 5, 'time_id' => 11],
            ['day_id' => 5, 'time_id' => 12],
            ['day_id' => 5, 'time_id' => 13],
            ['day_id' => 5, 'time_id' => 14],
            ['day_id' => 5, 'time_id' => 15],
            ['day_id' => 5, 'time_id' => 16],
            ['day_id' => 5, 'time_id' => 17],
            
            ['day_id' => 6, 'time_id' => 1],
            ['day_id' => 6, 'time_id' => 2],
            ['day_id' => 6, 'time_id' => 3],
            ['day_id' => 6, 'time_id' => 4],
            ['day_id' => 6, 'time_id' => 5],
            ['day_id' => 6, 'time_id' => 6],
            ['day_id' => 6, 'time_id' => 7],
            ['day_id' => 6, 'time_id' => 8],
            ['day_id' => 6, 'time_id' => 9],
            ['day_id' => 6, 'time_id' => 10],
            ['day_id' => 6, 'time_id' => 11],
            ['day_id' => 6, 'time_id' => 12],
            ['day_id' => 6, 'time_id' => 13],
            ['day_id' => 6, 'time_id' => 14],
            ['day_id' => 6, 'time_id' => 15],
            ['day_id' => 6, 'time_id' => 16],
            ['day_id' => 6, 'time_id' => 17],

            ['day_id' => 7, 'time_id' => 1],
            ['day_id' => 7, 'time_id' => 2],
            ['day_id' => 7, 'time_id' => 3],
            ['day_id' => 7, 'time_id' => 4],
            ['day_id' => 7, 'time_id' => 5],
            ['day_id' => 7, 'time_id' => 6],
        ];

        foreach ($times as $time) {
            TimeDay::create($time);
        }
    }
}
