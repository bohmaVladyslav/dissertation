@extends('main')

@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-4">
            @if($book->cover_path)
                <img src="{{ asset('storage/' . $book->cover_path) }}"
                     alt="Cover"
                     class="img-fluid rounded shadow">
            @else
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded"
                     style="height: 300px;">
                    No cover
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <h1>{{ $book->title }}</h1>

            <p class="text-muted">
                <strong>Автор:</strong> {{ $book->author ?? 'Не указан' }}
            </p>

            <hr>

            <div class="mb-3">
                <a href="{{ route('books.read', $book->id) }}"
                   class="btn btn-primary">
                    Открыть книгу
                </a>

                <a href="{{ asset('storage/' . $book->file_path) }}"
                   class="btn btn-outline-secondary"
                   download>
                    Скачать
                </a>

                <a href="{{ route('books.edit', $book->id) }}"
                   class="btn btn-info">
                    Обновить
                </a>

                <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger" onclick="return confirm('Удалить книгу?')">
                        Удалить
                    </button>
                </form>
                
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Информация</h5>

                    <p class="mb-1"><strong>ID:</strong> {{ $book->id }}</p>
                    <p class="mb-1"><strong>Файл:</strong> {{ basename($book->file_path) }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection