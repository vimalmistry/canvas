<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\View::class, function (Faker\Generator $faker) {
    $referers = collect([
        'https://google.com',
        'https://twitter.com',
        'https://facebook.com',
        'https://medium.com',
        'https://laravel-news.com',
        'https://reddit.com',
        'https://wordpress.com',
        'https://news.ycombinator.com',
        'https://hackernoon.com',
        'https://dev.to',
        'https://www.sitepoint.com',
    ]);

    $user_agents = collect([
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:69.0) Gecko/20100101 Firefox/69.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; rv:68.0) Gecko/20100101 Firefox/68.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.2 Safari/605.1.15',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
    ]);

    $timestamp = today()->subDays(rand(0, 30))->toDateTimeString();

    return [
        'post_id' => function () {
            return factory(\Canvas\Post::class)->create()->id;
        },
        'ip'      => $faker->ipv4,
        'agent'   => $user_agents->random(),
        'referer' => $referers->random(),
    ];
});
