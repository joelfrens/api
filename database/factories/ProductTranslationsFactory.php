<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\model\ProductTranslations;
use Faker\Generator as Faker;

$factory->define(ProductTranslations::class, function (Faker $faker) {
    return [
        'product_name' => $faker->word,
        'product_description' => $faker->paragraph,
        'product_id' => $faker->unique()->numberBetween(1, App\model\Product::count()),
        'language_id' => 1
    ];
});

