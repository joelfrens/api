<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\model\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'product_price' => $faker->numberBetween(10,20),
        'category_id' => 1
    ];
});
