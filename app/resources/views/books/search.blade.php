@extends('main')

@section('content')
<div class="container py-4">

    <h2 class="mb-4">Поиск книг</h2>

    {{-- Форма поиска --}}
    <form method="GET" action="{{ route('books.search') }}" class="mb-4">
        <div class="input-group">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Введите название книги или автора..."
            >

            <button class="btn btn-primary" type="submit">
                Поиск
            </button>
        </div>
    </form>

    {{-- Заголовок результатов --}}
    @if(request('q'))
        <h5 class="mb-3">
            Результаты для:
            <span class="text-primary">"{{ request('q') }}"</span>
        </h5>
    @endif

    {{-- Результаты --}}
    @if(request('q'))

        @if($books->count())
            <div class="row row-cols-1 row-cols-md-2 g-3">

                @foreach($books as $book)
                    <div class="col">
                        <a href="{{ route('books.show', $book->id) }}" class="card shadow-sm h-100 text-decoration-none text-dark">

                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $book->title }}
                                </h5>

                                @if($book->author)
                                    <p class="card-text mb-2">
                                        <span class="text-muted">Автор:</span>
                                        {{ $book->author }}
                                    </p>
                                @endif

                                <span class="badge bg-secondary">
                                    ID: {{ $book->id }}
                                </span>
                            </div>

                        </a>
                    </div>
                @endforeach

            </div>
        @else
            <div class="alert alert-warning mt-3">
                Ничего не найдено 😕
            </div>
        @endif

    @else
        <div class="alert alert-info">
            Введите запрос для поиска книг
        </div>
    @endif

</div>
@endsection