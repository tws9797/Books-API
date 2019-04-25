<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
      'isbn',
      'name',
      'title',
      'year',
    ];

    public function publisher(){
        $this->belongsTo(Publisher::class);
    }

    public function authors(){
      $this->belongsToMany(Author::class);
    }
}
