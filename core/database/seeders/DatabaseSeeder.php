<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            ShiftSeeder::class,
            DaySeeder::class,
            LessonTimeSeeder::class,
            TimeDaySeeder::class,
            TimeShiftSeeder::class,
            CategorySeeder::class,
            ModalitySeeder::class,
            CursoSeeder::class,
            ComponenteSeeder::class,
            ComponentCourseSeeder::class,
            ComponentUserSeeder::class,
            ScheduleSeeder::class,
            RoomSeeder::class,
        ]);

        // Gera permissões automaticamente para o painel "central"
        Artisan::call('shield:generate --all --panel=central');
    }
}
