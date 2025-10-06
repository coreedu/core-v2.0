<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modality;

class ModalitySeeder extends Seeder
{
    public function run(): void
    {
        $modalidades = [
            [
                'name' => 'Presencial',
                'description' => 'Aulas realizadas em ambiente físico, com presença obrigatória dos alunos.',
            ],
            [
                'name' => 'EAD',
                'description' => 'Educação a distância, com aulas e materiais acessados de forma online.',
            ],
            [
                'name' => 'Semipresencial',
                'description' => 'Modelo híbrido, combinando aulas presenciais e atividades online.',
            ],
        ];

        foreach ($modalidades as $dados) {
            Modality::updateOrCreate(
                ['name' => $dados['name']],
                ['description' => $dados['description']]
            );
        }
    }
}