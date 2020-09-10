<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\LegalRepresentative;
use Faker\Generator as Faker;

$factory->define(LegalRepresentative::class, function (Faker $faker) {
    return [
        'document' => $faker->word,
        'name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'address' => $faker->word,
        'email' => $faker->safeEmail,
    ];
});
