<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\audit;
use Faker\Generator as Faker;

$factory->define(Audit::class, function (Faker $faker) {
    return [
        'table' => $faker->word,
        'action' => $faker->randomElement(["update","disable","enable"]),
        'all_info' => $faker->json,
        'user_id' => factory(\App\User::class),
        'dependence_id' => $faker->numberBetween(-10000, 10000),
        'type_document_id' => $faker->numberBetween(-10000, 10000),
        'priority_id' => $faker->numberBetween(-10000, 10000),
        'context_type_id' => $faker->numberBetween(-10000, 10000),
        'type_identification_id' => $faker->numberBetween(-10000, 10000),
        'gender_id' => $faker->numberBetween(-10000, 10000),
        'user_table_id' => $faker->numberBetween(-10000, 10000),
        'entry_filing_id' => $faker->numberBetween(-10000, 10000),
        'outgoing_filing_id' => $faker->numberBetween(-10000, 10000)
    ];
});
