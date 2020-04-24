<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\Tag::class, function (Faker\Generator $faker) {
    return [
        'id'   => $faker->uuid,
        'slug' => $faker->slug,
        'name' => $faker->word,
    ];
});
