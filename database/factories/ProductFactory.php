<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'img' => $this->faker->ean13,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2,0,0),
            'stock' => 10,
            'user_id' => $this->faker->randomElement([2]),
        ];
    }
}
