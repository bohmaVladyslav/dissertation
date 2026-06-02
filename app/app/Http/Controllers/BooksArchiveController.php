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
    public function create()
    {
        return view('books.archive.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, BookArchiveProcessor $processor)
    {
        $request->validate([
            'archive' => ['required', 'file', 'mimes:zip,rar,7z'],
            'collection_name' => ['nullable', 'string', 'max:255'],
            // 'description' => ['nullable', 'string'],
        ]);

        $file = $request->file('archive');

        // 1. Сохраняем архив
        $archivePath = $file->store('archives');

        $fullArchivePath = storage_path('app/private/' . $archivePath);

        // 2. Создаём временную папку для распаковки
        $extractPath = storage_path('app/extracted/' . Str::uuid());

        if (!is_dir($extractPath)) {
            mkdir($extractPath, 0777, true);
        }

        // 3. Распаковка (ZIP)
        $zip = new \ZipArchive();

        $res = $zip->open($fullArchivePath);

        if ($res === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            Log::channel('info_file')->info($this->ZipStatusString($res));
            Log::channel('info_file')->info($fullArchivePath);
            return back()->withErrors(['archive' => 'Не удалось распаковать архив']);
        }

        // 4. Создаём коллекцию
        $collection = Collection::create([
            'name' => $request->collection_name ?? 'Коллекция ' . now()->format('Y-m-d H:i'),
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        // 5. Обработка книг
        $booksData = $processor->process($extractPath);

        $createdBooks = [];

        foreach ($booksData as $data) {
            Log::channel('info_file')->info(print_r($data, true));

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

        // 6. Очистка временных файлов (по желанию)
        // File::deleteDirectory($extractPath);

        // 7. Редирект на коллекцию
        return redirect()
            ->route('collections.show', $collection->id)
            ->with('success', 'Коллекция успешно создана');
    }

    private function ZipStatusString( $status )
    {
        switch( (int) $status )
        {
            case \ZipArchive::ER_OK           : return 'N No error';
            case \ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
            case \ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
            case \ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
            case \ZipArchive::ER_SEEK         : return 'S Seek error';
            case \ZipArchive::ER_READ         : return 'S Read error';
            case \ZipArchive::ER_WRITE        : return 'S Write error';
            case \ZipArchive::ER_CRC          : return 'N CRC error';
            case \ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
            case \ZipArchive::ER_NOENT        : return 'N No such file';
            case \ZipArchive::ER_EXISTS       : return 'N File already exists';
            case \ZipArchive::ER_OPEN         : return 'S Can\'t open file';
            case \ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
            case \ZipArchive::ER_ZLIB         : return 'Z Zlib error';
            case \ZipArchive::ER_MEMORY       : return 'N Malloc failure';
            case \ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
            case \ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
            case \ZipArchive::ER_EOF          : return 'N Premature EOF';
            case \ZipArchive::ER_INVAL        : return 'N Invalid argument';
            case \ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
            case \ZipArchive::ER_INTERNAL     : return 'N Internal error';
            case \ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
            case \ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
            case \ZipArchive::ER_DELETED      : return 'N Entry has been deleted';
            
            default: return sprintf('Unknown status %s', $status );
        }
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
