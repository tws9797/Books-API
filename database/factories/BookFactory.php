<?php

use Faker\Generator as Faker;

$factory->define(App\Book::class, function (Faker $faker) {
  return [
      'isbn' => $faker->unique()->bothify('???####'),
      'title' => $faker->name,
      'year' => $faker->year($max='now'),
      'publisher_id' => function(){
        return App\Publisher::all()->random();
      }
  ];
});
