<?php

use Faker\Generator as Faker;
use App\Article;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'id'        => $faker->$faker->randomDigitNotNull(5,12),
        'code'      => $faker->ean13(),
        'name'      => $faker->word(7),
        'sale_price'=> $faker->randomFloat(2,5,150),
        'stock'     => $faker->randomDigitNotNull(),
        'description'  => $faker->text,
        'category_id'  => $faker->numberBetween(1,4),
    ];
});
