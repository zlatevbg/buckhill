<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_uuid' => \App\Models\Category::all()->random()->uuid,
            'uuid' => $this->faker->uuid(),
            'title' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph(),
            'metadata' => [
                "brand" => \App\Models\Brand::all()->random()->uuid,
                "image" => \App\Models\File::all()->random()->uuid,
            ],
        ];
    }
}
