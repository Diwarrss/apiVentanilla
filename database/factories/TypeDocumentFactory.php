<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\TypeDocument;
use Faker\Generator as Faker;

$factory->define(TypeDocument::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'state' => $faker->boolean,
        'user_id' => factory(\App\User::class),
    ];
});
