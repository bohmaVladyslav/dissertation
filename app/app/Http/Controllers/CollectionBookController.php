<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CollectionBookController extends Controller
{
    public function add(Collection $collection, Book $book)
    {
        abort_unless($collection->user_id === Auth::id(), 403);

        // добавить без дублей
        $collection->books()->syncWithoutDetaching($book->id);

        return response()->json([
            'message' => 'Book added to collection',
        ]);
    }
    
    public function remove(Collection $collection, Book $book)
    {
        abort_unless($collection->user_id === Auth::id(), 403);

        $collection->books()->detach($book->id);

        return response()->json([
            'message' => 'Book removed from collection',
        ]);
    }
}
