<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Time\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            ['name' => 'Matutino', 'description' => ''],
            // ['name' => 'Matutino/Vespertino', 'description' => ''],
            ['name' => 'Vespertino', 'description' => ''],
            // ['name' => 'Vespertino/Noturno', 'description' => ''],
            ['name' => 'Noturno', 'description' => '']
        ];
        
        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
