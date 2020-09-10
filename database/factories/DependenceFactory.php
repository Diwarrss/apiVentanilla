<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Dependence;
use Faker\Generator as Faker;

$factory->define(Dependence::class, function (Faker $faker) {
    return [
        'identification' => $faker->word,
        'names' => $faker->word,
        'surnames' => $faker->word,
        'telephone' => $faker->word,
        'address' => $faker->word,
        'state' => $faker->boolean,
        'type' => $faker->randomElement(["dependence","person"]),
        'attachments' => $faker->text,
        'dependence_id' => factory(\App\Dependence::class),
        'type_identification_id' => factory(\App\TypeIdentification::class),
        'gender_id' => factory(\App\Gender::class),
        'user_id' => factory(\App\User::class),
    ];
});
