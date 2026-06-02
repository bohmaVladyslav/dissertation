<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\EpubService;
use App\Services\Fb2Service;
use App\Services\PdfService;
use App\Models\ReadingProgress;
use Illuminate\Support\Facades\Log;


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
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'string|max:5000',
            'file' => 'required|file|extensions:pdf,epub,txt,fb2|max:102400',
            'cover' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('file');
        $filePath = $file->storeAs(
            'books',
            uniqid() . '.' . $file->getClientOriginalExtension(),
            'public'
        );
        $fullPath = storage_path('app/public/' . $filePath);

        $coverPath = null;
        $meta = $this->extract($fullPath);


        if ($meta['cover']) {
            $coverPath = $meta['cover'];
        } else if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('books/covers/' . $request->user()->id, 'public');
        } else {
            return back()->withErrors([
                'cover' => 'Cover is required',
            ]);
        }

        $book = Book::create([
            'title' => $meta['title'] ?? $validated['title'],
            'author' => $meta['author'] ?? $validated['author'],
            'file_path' => $filePath,
            'cover_path' => $coverPath,
            'description' => $meta['description'] ?? $validated['description'],
            'user_id' => $request->user()->id,
        ]);

        return redirect('/books/' . $book->id);
    }

    protected function extract(string $fullPath)
    {
        $meta = null;
        $fileExtension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        switch ($fileExtension) {
            case 'txt':
                $meta = [
                    'title' => strtolower(pathinfo($fullPath, PATHINFO_BASENAME)),
                    'author' => null,
                    'description' => null,
                    'cover' => null,
                ];
                break;
            case 'pdf':
                $meta = app(PdfService::class)->extract($fullPath);
                break;
            case 'epub':
                $meta = app(EpubService::class)->extract($fullPath);
                break;
            case 'fb2':
                $meta = app(Fb2Service::class)->extract($fullPath);
                break;
            default:
                Log::channel('info_file')->info('Unsupported file format: ' . $fullPath);
                abort(415, 'Unsupported file format');
        }

        return $meta;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Book $book)
    {
        if ($request->user()->id !== $book->user_id) {
            abort(403);
        }

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book, EpubService $epub)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'string|max:5000',
            'file' => 'nullable|file|extensions:pdf,epub,txt,fb2|max:5120000',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }

            $book->file_path = $request->file('file')->store('books/files/' . $request->user()->id, 'public');
        }

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $book->cover_path = $request->file('cover')->store('books/covers/' . $request->user()->id, 'public');
        } else if ($request->hasFile('file')) {
            $meta = $this->extract(storage_path('app/public/' . $book->file_path));

            if ($meta['cover']) {
                $book->cover_path = $meta['cover'];
            } else {
                return back()->withErrors([
                    'cover' => 'Cover is required',
                ]);
            }
        }

        $book->title = $validated['title'];
        $book->description = $validated['description'];
        $book->author = $validated['author'];

        $book->save();

        return redirect('/books/' . $book->id);
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

    public function read(Request $request, Book $book)
    {
        $userId = $request->user()->id;

        $book->load(['progress' => function ($q) use ($userId) {
            $q->where('user_id', $userId);
        }]);

        $extension = strtolower(pathinfo($book->file_path, PATHINFO_EXTENSION));

        Log::info($extension);
        Log::info($book->file_path);

        $data = [
            'book' => $book,
            'type' => $extension,
        ];

        switch ($extension) {

            case 'txt':

                $content = Storage::disk('public')->get($book->file_path);

                $data['content'] = $content;

                break;

            case 'pdf':

                $data['fileUrl'] = asset('storage/' . $book->file_path);

                break;

            case 'epub':

                $data['fileUrl'] = asset('storage/' . $book->file_path);

                break;

            case 'fb2':

                $data['content'] = app(Fb2Service::class)
                    ->toHtml(Storage::disk('public')->path($book->file_path));

                break;
            default:

                abort(415, 'Unsupported file format');
        }

        return view('books.read', $data);
    }


    public function meta(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $name = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tmp', $name, 'local');
        $fullPath = storage_path("app/private/{$path}");

        $meta = $this->extract($fullPath);

        Log::channel('info_file')->info(print_r($meta, true));

        return response()->json([
            'title' => $meta['title'],
            'author' => $meta['author'],
            'description' => $meta['description'],
            'cover_url' => $meta['cover']
                ? asset('storage/' . $meta['cover'])
                : null,
        ]);
    }

    public function saveProgress(Request $request, Book $book)
    {
        $request->validate([
            'progress' => 'required|string'
        ]);

        ReadingProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
            ],
            [
                'progress' => $request->progress,
            ]
        );

        return response()->json([
            'success' => true
        ]);
    }
}
