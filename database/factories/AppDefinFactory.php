<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Hypocenter\LaravelSignature\Define\Models\AppDefine;
use Illuminate\Support\Str;

$factory->define(AppDefine::class, static function (Faker $faker) {
    return [
        'id' => Str::random(10),
        'name' => $faker->company,
        'secret' => Str::random(40),
    ];
});
