<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Time\LessonTime;

class LessonTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $times = [
            ['id' => 1, 'start' => '07:40', 'end' => '08:30'],
            ['id' => 2, 'start' => '08:30', 'end' => '09:20'],
            ['id' => 3, 'start' => '09:30', 'end' => '10:20'],
            ['id' => 4, 'start' => '10:20', 'end' => '11:10'],
            ['id' => 5, 'start' => '11:20', 'end' => '12:10'],
            ['id' => 6, 'start' => '12:10', 'end' => '13:00'],
            ['id' => 7, 'start' => '13:00', 'end' => '13:50'],
            ['id' => 8, 'start' => '13:50', 'end' => '14:40'],
            ['id' => 9, 'start' => '14:50', 'end' => '15:40'],
            ['id' => 10,'start' =>  '15:40', 'end' => '16:30'],
            ['id' => 11,'start' =>  '16:30', 'end' => '17:20'],
            ['id' => 12,'start' =>  '17:20', 'end' => '18:10'],
            ['id' => 13,'start' =>  '18:10', 'end' => '19:00'],
            ['id' => 14,'start' =>  '19:00', 'end' => '19:50'],
            ['id' => 15,'start' =>  '19:50', 'end' => '20:40'],
            ['id' => 16,'start' =>  '20:50', 'end' => '21:40'],
            ['id' => 17,'start' =>  '21:40', 'end' => '22:30'],
        ];

        foreach ($times as $time) {
            LessonTime::create($time);
        }
    }
}
