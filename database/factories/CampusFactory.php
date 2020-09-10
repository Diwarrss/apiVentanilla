<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Campus;
use Faker\Generator as Faker;

$factory->define(Campus::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initials' => $faker->word(5),
        'address' => $faker->word,
        'telephone' => $faker->word,
        'state' => $faker->boolean,
        'company_id' => factory(\App\Company::class),
    ];
});
