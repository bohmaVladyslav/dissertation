@extends('main')

@section('content')
<div class="container">

    {{-- Заголовок --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $collection->title }}</h1>

        <div>
            <a href="{{ route('collections.edit', $collection->id) }}" class="btn btn-warning">
                Редактировать
            </a>

            <form action="{{ route('collections.destroy', $collection->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger" onclick="return confirm('Удалить коллекцию?')">
                    Удалить
                </button>
            </form>
        </div>
    </div>

    {{-- Книги --}}
    <div class="card">
        <div class="card-header">
            Книги в коллекции
        </div>

        <div class="card-body">
            @if($collection->books->count())
                <ul class="list-group list-group-flush">
                    @foreach($collection->books as $book)
                        <li class="list-group-item d-flex justify-content-between align-items-center">

                            <div>
                                <strong>{{ $book->title }}</strong><br>
                                <small class="text-muted">{{ $book->author }}</small>
                            </div>

                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                Открыть
                            </a>

                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">В коллекции пока нет книг</p>
            @endif
        </div>
    </div>

</div>
@endsection