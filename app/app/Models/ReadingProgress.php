<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'book_id', 'progress'])]

class ReadingProgress extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'progress',
    ];
}