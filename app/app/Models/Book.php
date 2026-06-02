<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'author', 'description', 'file_path', 'cover_path', 'user_id'])]
class Book extends Model
{
    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function progress()
    {
        return $this->hasOne(ReadingProgress::class);
    }
}
