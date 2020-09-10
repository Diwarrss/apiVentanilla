<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TypeIdentification;
use Faker\Generator as Faker;

$factory->define(TypeIdentification::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initials' => $faker->word(5),
        'state' => $faker->boolean,
        'user_id' => factory(\App\User::class),
    ];
});
