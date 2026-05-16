<?php

namespace App\Models;

use Illuminate\Console\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Book;

#[Fillable('name')]
class Collection extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
