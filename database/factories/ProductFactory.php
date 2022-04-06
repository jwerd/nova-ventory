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
    public function definition()
    {
        $names = [
            'Red Dresser',
            'Blue Dresser',
            'Red Hutch',
            'Black Hutch',
            'Tallboy',
        ];
        return [
            'name' => $this->faker->randomElement($names),
            'purchased_price' => $this->faker->randomFloat(1,100),
            'list_price' => $this->faker->randomFloat(1,100),
        ];
    }
}
