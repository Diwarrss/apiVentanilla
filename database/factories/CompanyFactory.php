<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initials' => 'PRUEBA',
        'nit' => $faker->randomNumber,
        'address' => 'https://www.gridsoft.co/',
        'phone' => $faker->e164PhoneNumber,
        'image' => 'img/logo.png',
        'logo' => 'img/logo.png',
        'logo_name' => 'logo.png',
        'state' => $faker->boolean,
        'type' => $faker->randomElement(["basic","professional","avanced"]),
        'legal_representative_id' => factory(\App\LegalRepresentative::class),
    ];
});
