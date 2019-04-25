<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      factory(App\User::class, 5)->create();
      factory(App\Author::class, 30)->create();
      factory(App\Publisher::class, 30)->create();
      factory(App\Book::class, 50)->create();
      foreach( range(1,30) as $index){
        DB::table('author_book')->insert(
          [
            'author_id' => App\Author::select('id')->inRandomOrder()->first()->id,
            'book_id' => App\Book::select('id')->inRandomOrder()->first()->id
          ]
        );
      }
    }
}
