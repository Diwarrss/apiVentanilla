<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\EntryFiling;
use Faker\Generator as Faker;

$factory->define(EntryFiling::class, function (Faker $faker) {
    return [
        'cons_year' => $faker->numberBetween(-10000, 10000),
        'year' => $faker->numberBetween(-10000, 10000),
        'title' => $faker->sentence(4),
        'settled' => $faker->word,
        'access_level' => $faker->randomElement(["public","restricted"]),
        'means_document' => $faker->randomElement(["fisic","digital","fisic\/digital"]),
        'folios' => $faker->numberBetween(-10000, 10000),
        'annexes' => $faker->numberBetween(-10000, 10000),
        'subject' => $faker->text,
        'key_words' => $faker->word,
        'attachments' => $faker->text,
        'state' => $faker->boolean,
        'user_id' => factory(\App\User::class),
        'campus_id' => factory(\App\Campus::class),
        'priority_id' => factory(\App\Priority::class),
        'dependence_id' => factory(\App\Dependence::class),
        'type_document_id' => factory(\App\TypeDocument::class),
        'context_type_id' => factory(\App\ContextType::class),
        'dependence_id' => factory(\App\Dependence::class),
    ];
});
