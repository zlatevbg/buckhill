<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence;

        return [
            'uuid' => $this->faker->uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->realText(),
            'metadata' => [
                "author" => $this->faker->name(),
                "image" => \App\Models\File::all()->random()->path,
            ],
        ];
    }
}
