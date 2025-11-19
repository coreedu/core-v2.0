<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Componente;

class ComponenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $componentes = [
            [
                'nome' => 'Linguagem de Programação I',
                'abreviacao' => 'LP1',
                'horasSemanais' => 4,
                'horasTotais' => 80,
            ],
            [
                'nome' => 'Banco de Dados',
                'abreviacao' => 'BD',
                'horasSemanais' => 3,
                'horasTotais' => 60,
            ],
            [
                'nome' => 'Estrutura de Dados',
                'abreviacao' => 'ED',
                'horasSemanais' => 4,
                'horasTotais' => 80,
            ],
            [
                'nome' => 'Engenharia de Software',
                'abreviacao' => 'ES',
                'horasSemanais' => 2,
                'horasTotais' => 40,
            ],
            [
                'nome' => 'Desenvolvimento Web',
                'abreviacao' => 'DW',
                'horasSemanais' => 4,
                'horasTotais' => 80,
            ],
        ];

        foreach ($componentes as $componente) {
            Componente::create($componente);
        }

        // Componente::factory()->count(20)->create();
    }
}