@extends('main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    Загрузка архива книг
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('books.archive.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="archive" class="form-label">Архив книг (ZIP / RAR)</label>
                            <input 
                                type="file" 
                                class="form-control"
                                id="archive"
                                name="archive"
                                accept=".zip,.rar,.7z"
                                required
                            >
                            <div class="form-text">
                                Поддерживаются архивы: ZIP, RAR, 7Z. Внутри должны быть книги (epub, fb2, pdf, txt).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="collection_name" class="form-label">Название коллекции (необязательно)</label>
                            <input 
                                type="text"
                                class="form-control"
                                id="collection_name"
                                name="collection_name"
                                placeholder="Например: Фантастика 2026"
                            >
                        </div>

                        {{-- <div class="mb-3">
                            <label for="description" class="form-label">Описание (необязательно)</label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Комментарий к коллекции..."
                            ></textarea>
                        </div> --}}

                        <button type="submit" class="btn btn-primary">
                            Загрузить и обработать архив
                        </button>

                        @error('archive')
                        <div class="mb-3">
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        </div>
                        @enderror
                        </div>
                    </form>

                </div>
            </div>

            <div class="mt-3 text-muted small">
                После загрузки система автоматически:
                <ul>
                    <li>распакует архив</li>
                    <li>создаст коллекцию (если не указана — создаст автоматически)</li>
                    <li>добавит книги в базу</li>
                    <li>попытается извлечь метаданные</li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection