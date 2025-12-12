<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Sala de Aula', 'description' => 'Espaço destinado às aulas regulares'],
            ['name' => 'Laboratório de Informática', 'description' => 'Laboratório com computadores para uso educacional'],
            // ['name' => 'Laboratório de Ciências', 'description' => 'Espaço para experimentos científicos'],
            ['name' => 'Biblioteca', 'description' => 'Espaço de leitura e estudo'],
            ['name' => 'Auditório', 'description' => 'Espaço para apresentações e eventos'],
            // ['name' => 'Sala de Reunião', 'description' => 'Espaço reservado para reuniões'],
            ['name' => 'Quadra Esportiva', 'description' => 'Espaço para prática de esportes'],
            // ['name' => 'Refeitório', 'description' => 'Espaço para refeições dos alunos e funcionários'],
            ['name' => 'Secretaria', 'description' => 'Atendimento administrativo da escola'],
            ['name' => 'Diretoria', 'description' => 'Sala da direção escolar'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}