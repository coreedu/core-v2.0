<?php

namespace Database\Factories;

use App\Models\Componente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Componente>
 */
class ComponenteFactory extends Factory
{
    protected $model = Componente::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Gera um nome com 3 ou 4 palavras únicas, mais adequado para títulos.
            'nome' => $this->faker->unique()->words($this->faker->numberBetween(3, 4), true), 
            
            // Gera 3 letras maiúsculas aleatórias para abreviação.
            'abreviacao' => strtoupper($this->faker->bothify('???')), 
            
            // Gera um float (decimal) opcional para horas semanais (ex: 4.0 a 20.0).
            'horasSemanais' => $this->faker->optional()->randomFloat(2, 4, 20),
            
            // Gera um float (decimal) opcional para horas totais (ex: 40.0 a 400.0).
            'horasTotais' => $this->faker->optional()->randomFloat(2, 40, 400),
        ];
    }
}
