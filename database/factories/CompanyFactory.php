<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initials' => 'PRUEBA',
        'nit' => $faker->randomNumber,
        'address' => $faker->word,
        'phone' => $faker->e164PhoneNumber,
        'image' => 'img/logotipo.png',
        'logo' => 'img/logotipo.png',
        'state' => $faker->boolean,
        'type' => $faker->randomElement(["basic","professional","avanced"]),
        'legal_representative_id' => factory(\App\LegalRepresentative::class),
    ];
});
