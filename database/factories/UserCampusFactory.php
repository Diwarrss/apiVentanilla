<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserCampus;
use Faker\Generator as Faker;

$factory->define(UserCampus::class, function (Faker $faker) {
    return [
        'campus_id' => factory(\App\Campus::class),
        'user_id' => factory(\App\User::class),
    ];
});
