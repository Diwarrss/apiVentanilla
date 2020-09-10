<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\People;
use Faker\Generator as Faker;

$factory->define(People::class, function (Faker $faker) {
    return [
        'identification' => $faker->word,
        'names' => $faker->word,
        'surnames' => $faker->word,
        'telephone' => $faker->word,
        'address' => $faker->word,
        'email' => $faker->safeEmail,
        'state' => $faker->boolean,
        'type' => $faker->randomElement(["company","person"]),
        'user_id' => factory(\App\User::class),
        'type_identification_id' => factory(\App\TypeIdentification::class),
        'gender_id' => factory(\App\Gender::class),
        'people_id' => factory(\App\People::class),
    ];
});
