<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->sentence(),
            'path' => $this->faker->imageUrl(640, 480, 'animals', true),
            'size' => $this->faker->numberBetween(100 * 1024, 1000 * 1024),
            'type' => 'image/png',
        ];
    }
}
