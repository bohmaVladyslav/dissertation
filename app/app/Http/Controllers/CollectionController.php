<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CollectionController extends Controller
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
        return view('collections.create', [
            'books' => Book::all(),
            'user' => $request->user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::channel('info_file')->info('hvat?', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'books' => 'required|array',
        ]);

        Log::channel('info_file')->info('validated');

        $collection = Collection::create([
            'name' => $validated['title'],
            'user_id' => $request->user()->id,
        ]);

        Log::channel('info_file')->info('collection created');

        $collection->books()->sync($request->books);

        Log::channel('info_file')->info('books stored');

        return redirect('/collections/' . $collection->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Collection $collection)
    {
        return view('collections.show', [
            'collection' => $collection,
            'user' => $request->user(),
            'books' => $collection->books
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Collection $collection)
    {
        return view('collections.edit', [
            'collection' => $collection,
            'user' => $request->user(),
            'books' => Book::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Collection::update($validated);

        return redirect('/collections/' . $collection->id);
    }

    public function deleteBook(Request $request, Collection $collection, Book $book)
    {
        $collection->books()->detach($book);

        return redirect('/collections/' . $collection->id);
    }


    public function destroyAll(Collection $collection)
    {
        $books = $collection->books()->get();

        $collection->books()->detach();

        foreach ($books as $book) {
            $book->delete();
        }

        $collection->delete();

        return redirect('/user');
    }

    public function download(Collection $collection)
    {
        $books = $collection->books;

        if ($books->isEmpty()) {
            return back()->withErrors(['collection' => 'Коллекция пуста']);
        }

        $tempDir = storage_path('app/tmp/' . Str::uuid());

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($books as $book) {
            $sourcePath = storage_path('app/public/' . $book->file_path);

            if (!file_exists($sourcePath)) {
                continue;
            }

            $extension = pathinfo($book->file_path, PATHINFO_EXTENSION);

            $safeTitle = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $book->title);
            $safeTitle = trim($safeTitle);
            $safeTitle = str_replace(' ', '_', $safeTitle);

            if (!$safeTitle) {
                $safeTitle = 'book_' . $book->id;
            }

            $fileName = $safeTitle . '.' . $extension;

            copy($sourcePath, $tempDir . '/' . $fileName);
        }

        $zipName = $collection->name . '.zip';
        $zipPath = storage_path('app/tmp/' . $zipName);

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['collection' => 'Unable to create the archive']);
        }

        $files = scandir($tempDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $zip->addFile($tempDir . '/' . $file, $file);
        }

        $zip->close();

        foreach (glob($tempDir . '/*') as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect('/user');
    }
}
