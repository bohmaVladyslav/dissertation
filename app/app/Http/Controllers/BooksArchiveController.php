<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Services\BookArchiveProcessor;
use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class BooksArchiveController extends Controller
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
    public function create(Request $request)
    {
        return view('books.archive.create', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, BookArchiveProcessor $processor)
    {
        $request->validate([
            'archive' => ['required', 'file', 'mimes:zip,rar,7z'],
            'collection_name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('archive');

        $archivePath = $file->store('archives');

        $fullArchivePath = storage_path('app/private/' . $archivePath);

        $extractPath = storage_path('app/extracted/' . Str::uuid());

        if (!is_dir($extractPath)) {
            mkdir($extractPath, 0777, true);
        }

        try {
            $processor->extractArchive($fullArchivePath, $extractPath);
        } catch (\Throwable $th) {
            return back()->withErrors(['archive' => 'Unable to extract the archive: ' . $th->getMessage()]);
        }

        $collection = Collection::create([
            'name' => $request->collection_name ?? 'Collection ' . now()->format('Y-m-d H:i'),
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        $booksData = $processor->process($extractPath);

        $createdBooks = [];

        foreach ($booksData as $data) {
            $createdBooks[] = Book::create([
                'user_id'       => $request->user()->id,
                'title'         => $data['title'],
                'author'        => $data['author'],
                'description'   => $data['description'],
                'file_path'     => $this->moveBookFile($data['file_path']),
                'cover_path'         => $data['cover'],
            ]);
        }

        $collection->books()->sync($createdBooks);

        return redirect()
            ->route('collections.show', $collection->id);
    }

    /**
     * Перемещение книги в постоянное хранилище
     */
    private function moveBookFile(string $path): string
    {
        $filename = basename($path);

        $newPath = 'books/' . Str::uuid() . '_' . $filename;

        Storage::disk('public')->put(
            $newPath,
            file_get_contents($path)
        );

        return $newPath;
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //
    }
}
