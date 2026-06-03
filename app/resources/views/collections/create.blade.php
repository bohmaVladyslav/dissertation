@extends('main')

@section('content')
<div class="container">
    <h1 class="mb-4">Create collection</h1>

    <form action="{{ route('collections.store') }}" method="POST">
        @csrf

        {{-- Название коллекции --}}
        <div class="mb-3">
            <label for="title" class="form-label">Name of collection</label>
            <input
                type="text"
                name="title"
                id="title"
                class="form-control"
                value="{{ old('title') }}"
                required
            >
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Выбор книг --}}
        <div class="mb-3">
            <label class="form-label">Books</label>

            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                @foreach ($books as $book)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="books[]"
                            value="{{ $book->id }}"
                            id="book_{{ $book->id }}"
                            {{ in_array($book->id, old('books', [])) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="book_{{ $book->id }}">
                            {{ $book->title }} — {{ $book->author }}
                        </label>
                    </div>
                @endforeach
            </div>

            @error('books')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Submit
        </button>
    </form>
</div>
@endsection