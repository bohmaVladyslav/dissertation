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

            <a href="{{ route('collections.download', $collection->id) }}" download class="btn btn-primary">
                Скачать
            </a>

            <form action="{{ route('collections.destroy', $collection->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger" onclick="return confirm('Удалить коллекцию?')">
                    Удалить
                </button>
            </form>

            <form action="{{ route('collections.destroyAll', $collection->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger" onclick="return confirm('Удалить коллекцию с книгами?')">
                    Удалить вместе с книгами
                </button>
            </form>
        </div>
    </div>

    {{-- Книги --}}
    <div class="card">
        <div class="card-header">
            {{ $collection->name }}
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

                            <div class="d-flex gap-2">
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                    Открыть
                                </a>
    
                                <form action="{{ route('collection.deleteBook', [$collection->id, $book->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
    
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить коллекцию?')">
                                        Удалить из коллекции
                                    </button>
                                </form>
                                
                                <form action="{{ route('books.destroy', [$book->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
    
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Удалить книгу?')">
                                        Удалить книгу
                                    </button>
                                </form>
                            </div>


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