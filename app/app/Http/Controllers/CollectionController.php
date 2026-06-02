<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\Book;

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
            'books' => Book::all()
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
    public function show(Collection $collection)
    {
        return view('collections.show', [
            'collection' => $collection,
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection) {
        return view('collections.edit', [
            'collection' => $collection,
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect('/collections');
    }
}
