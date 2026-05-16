<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'file' => 'required|file|mimes:pdf,epub,doc,docx|max:51200',
            'cover' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $filePath = $request->file('file')->store('books/files/'. $request->user()->id, 'public');
        $coverPath = $request->file('cover')->store('books/covers'. $request->user()->id, 'public');

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'description' => $validated['description'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',

            'file' => 'nullable|file|mimes:pdf,epub,doc,docx|max:51200',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }

            $book->file_path = $request->file('file')->store('books/files'. $request->user()->id, 'public');
        }

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $book->cover_path = $request->file('cover')->store('books/covers'. $request->user()->id, 'public');
        }

        $book->title = $validated['title'];
        $book->author = $validated['author'];

        $book->save();

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }

        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }
}
