<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PromotionFactory extends Factory
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
                "valid_from" => date('Y-m-d', strtotime('+' . $this->faker->randomDigitNot(0) . ' day')),
                "valid_to" => date('Y-m-d', strtotime('+' . $this->faker->randomDigitNot(0) . ' week ' . $this->faker->randomDigitNot(0) . ' day')),
                "image" => \App\Models\File::all()->random()->path,
            ],
        ];
    }
}
