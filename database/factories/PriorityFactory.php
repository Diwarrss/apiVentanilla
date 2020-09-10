<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Priority;
use Faker\Generator as Faker;

$factory->define(Priority::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initials' => $faker->word,
        'state' => $faker->boolean,
        'days' => $faker->numberBetween(-10000, 10000),
        'user_id' => factory(\App\User::class),
    ];
});
