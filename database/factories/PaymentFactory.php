<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
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
        ];
    }

    /**
     * Indicate that the payment method is credit_card.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function creditCard()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'credit_card',
                'details' => [
                    "holder_name" => $this->faker->name(),
                    "number" => $this->faker->numerify('################'),
                    "ccv" => $this->faker->randomNumber(3, true),
                    "expire_date" => date('y/m', strtotime('+' . $this->faker->randomDigitNot(0) . ' year ' . $this->faker->randomDigitNot(0) . ' month')),
                ],
            ];
        });
    }

    /**
     * Indicate that the payment method is cash_on_delivery.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cashOnDelivery()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'cash_on_delivery',
                'details' => [
                    "first_name" => $this->faker->firstName(),
                    "last_name" => $this->faker->lastName(),
                    "address" => $this->faker->address(),
                ],
            ];
        });
    }

    /**
     * Indicate that the payment method is bank_transfer.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function bankTransfer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'bank_transfer',
                'details' => [
                    "swift" => $this->faker->regexify('US[A-Z]{6}'),
                    "iban" => $this->faker->numerify('US-####-####-####-####'),
                    "name" => $this->faker->name(),
                ],
            ];
        });
    }
}
