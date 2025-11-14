<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\Componente;
use Illuminate\Support\Facades\DB;


class ComponentCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $componentIds = Componente::pluck('id');

        Curso::all()->each(function($course) use($componentIds){
            $qtdModules = $course->qtdModulos;

            for ($module = 1; $module <= $qtdModules; $module++) {
                
                $numComponents = rand(2, 5); 
                
                $components = $componentIds->random($numComponents);
                
                $dataPivot = $components->map(function ($componentId) use ($course, $module) {
                    return [
                        'course' => $course->id,
                        'component' => $componentId,
                        'module' => $module,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();
                
                DB::table('component_course')->insertOrIgnore($dataPivot);
            }
        });
    }
}
