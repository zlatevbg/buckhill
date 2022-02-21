<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $products = \App\Models\Product::all()->random($this->faker->numberBetween(1, 3));
        $orderProducts = [];
        foreach ($products as $product) {
            array_push($orderProducts, [
                'product' => $product->uuid,
                'quantity' => $this->faker->randomDigitNot(0),
            ]);
        }

        $price = $this->faker->randomFloat(2, 10, 1000);

        return [
            'user_id' => \App\Models\User::where('email', 'user@buckhill.co.uk')->value('id'),
            'order_status_id' => \App\Models\OrderStatus::all()->random()->id,
            'payment_id' => \App\Models\Payment::all()->random()->id,
            'uuid' => $this->faker->uuid(),
            'products' => $orderProducts,
            'address' => [
                "billing" => $this->faker->address(),
                "shipping" => $this->faker->address(),
            ],
            'delivery_fee' => ($price > 500 ? 15 : null),
            'amount' => $price,
            'shipped_at' => date('Y-m-d H:i:s', strtotime('-' . $this->faker->randomDigitNot(0) . ' year ' . $this->faker->randomDigitNot(0) . ' month ' . $this->faker->randomDigitNot(0) . ' day')),
        ];
    }
}
