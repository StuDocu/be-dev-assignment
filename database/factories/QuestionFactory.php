<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Questionnaire\Models\Question;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'question' => $faker->text . '?',
        'answer' => $faker->text
    ];
});
