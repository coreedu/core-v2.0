<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $courses = [
        //     [
        //         'turno' => 1, 
        //         'nome' => 'Teste',
        //         'abreviacao' => 'TT',
        //         'qtdModulos' => 6,
        //         'modalidade' => null,
        //         'horas' => 200,
        //         'horasEstagio' => null,
        //         'horasTg' => null,

        //     ],
        // ];

        // foreach ($courses as $c) {
        //     Curso::create($c);
        // }

        // Criar 10 registros aleatórios
        Curso::factory()->count(10)->create(); 
    }
}
