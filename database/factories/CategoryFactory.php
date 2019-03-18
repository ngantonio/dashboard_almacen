<?php

use Faker\Generator as Faker;
use App\Category;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name'        => $faker->company(),
        'description' => $faker->catchPhrase()    
        //'description' => $faker->realText($maxNbChars = 40)
    ];
});

