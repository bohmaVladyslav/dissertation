<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;


class Book extends Model
{
    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }
}
