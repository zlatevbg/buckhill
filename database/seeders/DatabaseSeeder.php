<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(1)->admin()->create();
        \App\Models\User::factory(1)->user()->create();
        \App\Models\User::factory(9)->create();
        \App\Models\Brand::factory(10)->create();
        \App\Models\File::factory(10)->create();
        \App\Models\Category::factory(10)->create();
        \App\Models\Product::factory(10)->create();
        \App\Models\Post::factory(10)->create();
        \App\Models\OrderStatus::factory(10)->create();
        \App\Models\Payment::factory(4)->creditCard()->create();
        \App\Models\Payment::factory(4)->cashOnDelivery()->create();
        \App\Models\Payment::factory(4)->bankTransfer()->create();
        \App\Models\Order::factory(10)->create();
        \App\Models\Promotion::factory(10)->create();
    }
}
