<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Curso>
 */
class CursoFactory extends Factory
{
    protected $model = Curso::class;

    /**
     * O Faker customizado.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return FakerFactory::create('pt_BR');
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'turno' => $this->faker->optional()->numberBetween(1, 3), 
            // 'nome' => $this->faker->words(3, true),
            'nome' => $this->faker->unique()->jobTitle(),
            'abreviacao' => $this->faker->bothify('???'), 
            'qtdModulos' => $this->faker->numberBetween(1, 8), 
            'modalidade' => $this->faker->optional()->numberBetween(1, 3), 
            'horas' => $this->faker->numberBetween(100, 3000),
            'horasEstagio' => $this->faker->optional()->numberBetween(50, 400), 
            'horasTg' => $this->faker->optional()->numberBetween(0, 100), 
        ];
    }
}
