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
            ['cod' => 1, 'name' => 'Matutino', 'description' => ''],
            ['cod' => 12, 'name' => 'Matutino/Vespertino', 'description' => ''],
            ['cod' => 2, 'name' => 'Vespertino', 'description' => ''],
            ['cod' => 23, 'name' => 'Vespertino/Noturno', 'description' => ''],
            ['cod' => 3, 'name' => 'Noturno', 'description' => '']
        ];
        
        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
