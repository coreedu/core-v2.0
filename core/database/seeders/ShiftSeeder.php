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
        Shift::create([
            'cod' => 1,
            'name' => 'Matutino',
            'description' => ''
        ]);

        Shift::create([
            'cod' => 12,
            'name' => 'Matutino/Vespertino',
            'description' => ''
        ]);

        Shift::create([
            'cod' => 2,
            'name' => 'Vespertino',
            'description' => ''
        ]);

        Shift::create([
            'cod' => 23,
            'name' => 'Vespertino/Noturno',
            'description' => ''
        ]);
        
        Shift::create([
            'cod' => 3,
            'name' => 'Noturno',
            'description' => ''
        ]);
    }
}
