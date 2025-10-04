<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TimeShift;

class TimeShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            ['shift_cod' => 1, 'lesson_time_id' => 1],
            ['shift_cod' => 1, 'lesson_time_id' => 2],
            ['shift_cod' => 1, 'lesson_time_id' => 3],
            ['shift_cod' => 1, 'lesson_time_id' => 4],
            ['shift_cod' => 1, 'lesson_time_id' => 5],
            ['shift_cod' => 1, 'lesson_time_id' => 6],
            
            ['shift_cod' => 12, 'lesson_time_id' => 7],

            ['shift_cod' => 2, 'lesson_time_id' => 8],
            ['shift_cod' => 2, 'lesson_time_id' => 9],
            ['shift_cod' => 2, 'lesson_time_id' => 10],
            ['shift_cod' => 2, 'lesson_time_id' => 11],
            ['shift_cod' => 2, 'lesson_time_id' => 12],
            
            ['shift_cod' => 23, 'lesson_time_id' => 13],

            ['shift_cod' => 3, 'lesson_time_id' => 14],
            ['shift_cod' => 3, 'lesson_time_id' => 15],
            ['shift_cod' => 3, 'lesson_time_id' => 16],
            ['shift_cod' => 3, 'lesson_time_id' => 17],
        ];

        foreach ($times as $time) {
            TimeShift::create($time);
        }
    }
}
