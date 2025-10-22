<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'version' => '2024.1',
                'shift_cod' => 1, // Matutino
                'course_id' => 1, // Teste course
                'modality_id' => null,
                'module_id' => 1,
                'status' => false,
            ],
            [
                'version' => '2024.1',
                'shift_cod' => 2, // Vespertino
                'course_id' => 1, // Teste course
                'modality_id' => null,
                'module_id' => 1,
                'status' => false,
            ],
            [
                'version' => '2024.1',
                'shift_cod' => 3, // Noturno
                'course_id' => 1, // Teste course
                'modality_id' => null,
                'module_id' => 1,
                'status' => false,
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}
