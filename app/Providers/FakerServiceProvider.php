<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Faker\PetShopProvider;
use Faker\{Factory, Generator};

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new PetShopProvider($faker));

            return $faker;
        });
    }
}
