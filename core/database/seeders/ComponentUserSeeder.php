<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Componente;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ComponentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $componentIds = Componente::pluck('id');

        User::all()->each(function($user) use($componentIds){

            $numComponents = rand(2, 5); 
            
            $components = $componentIds->random($numComponents);
            
            $dataPivot = $components->map(function ($componentId) use ($user) {
                return [
                    'instructor' => $user->id,
                    'component' => $componentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
            
            DB::table('component_instructor')->insertOrIgnore($dataPivot);
        });
    }
}
