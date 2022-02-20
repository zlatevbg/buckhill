<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'is_admin' => 0,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$U0CXiKvqEvwFFnfv84Vcvu/5uaOkXwztz2HsOmqHhMx3WqHCZUdOe', // userpassword
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'is_marketing' => 0,
        ];
    }

    /**
     * Create a static user used for Orders.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function user()
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'Buckhill',
                'last_name' => 'User',
                'email' => 'user@buckhill.co.uk',
            ];
        });
    }

    /**
     * Indicate that the model is admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'Buckhill',
                'last_name' => 'Admin',
                'is_admin' => 1,
                'email' => 'admin@buckhill.co.uk',
                'password' => '$2y$10$TuYBfQKe5bAtyP/79ahMW.eFuUs6kccyV7tjjnAZeB6vkSstJ.hzK', // admin
            ];
        });
    }
}
