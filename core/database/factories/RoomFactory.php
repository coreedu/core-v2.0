<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryId = null;

        if (Category::exists()) {
            $categoryId = Category::inRandomOrder()->first()->id;
        }

        return [
            'name' => $this->faker->words(2, true), 
            'number' => $this->faker->numberBetween(1, 200),
            'type' => $categoryId
        ];
    }
}
