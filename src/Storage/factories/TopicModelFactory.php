<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\Topic::class, function (Faker\Generator $faker) {
    return [
        'id'   => $faker->uuid,
        'slug' => $faker->slug,
        'name' => $faker->word,
    ];
});
