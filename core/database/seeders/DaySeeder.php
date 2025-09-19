<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Time\Day;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            ['cod' => 1, 'name' => 'Domingo'],
            ['cod' => 2, 'name' => 'Segunda-feira'],
            ['cod' => 3, 'name' => 'Terça-feira'],
            ['cod' => 4, 'name' => 'Quarta-feira'],
            ['cod' => 5, 'name' => 'Quinta-feira'],
            ['cod' => 6, 'name' => 'Sexta-feira'],
            ['cod' => 7, 'name' => 'Sábado']
        ];

        foreach ($days as $day) {
            Day::create($day);
        }
    }
}
