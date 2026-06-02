@extends('main')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">

        <h3 class="mb-4">Редактировать книгу</h3>

        <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')

            {{-- Название --}}
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control"
                       value="{{ $book->title }}"
                       required>
            </div>

            {{-- Автор --}}
            <div class="mb-3">
                <label class="form-label">Автор</label>
                <input type="text"
                       name="author"
                       class="form-control"
                       id="author"
                       value="{{ $book->author }}"
                       required>
            </div>

            {{-- Описание --}}
            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea name="description"
                        id="description"
                        class="form-control"
                        rows="4">{{ $book->description }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Обложка</label>

                {{-- Текущая обложка --}}
                @if($book->cover_path)
                    <div class="mb-2" id="oldCover">
                        <img src="{{ asset('storage/' . $book->cover_path) }}"
                            alt="Cover"
                            style="max-width:150px;">
                    </div>
                @else
                    <p class="text-muted">Обложка не загружена</p>
                @endif

                {{-- Кнопка замены --}}
                <button type="button"
                        onclick="document.getElementById('coverInput').click()">
                    Заменить обложку
                </button>

                <input type="file"
                    name="cover"
                    id="coverInput"
                    class="form-control mt-2">

                {{-- preview новой выбранной --}}
                <img id="coverPreview"
                    style="max-width:150px; display:none; margin-top:10px;">
            </div>

            {{-- Файл книги --}}
            <div class="mb-3">
                <label class="form-label">Файл книги</label>
                <input type="file"
                       name="file"
                       id="file"
                       class="form-control"
                       accept=".pdf,.epub,.txt">
            </div>

            {{-- Кнопка --}}
            <button type="submit" class="btn btn-primary w-100">
                Добавить книгу
            </button>

        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelector('input[name="file"]').addEventListener('change', async function (e) {
    const file = e.target.files[0];

    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    const response = await fetch('/books/epub-meta', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    });

    const data = await response.json();

    if (data.title) {
        document.getElementById('title').value = data.title;
    }

    if (data.author) {
        document.querySelector('input[name="author"]').value = data.author;
    }

    if (data.description) {
        document.querySelector('textarea[name="description"]').value = data.description;
    }

    if (data.cover_url) {
        const img = document.getElementById('coverPreview');
        img.src = data.cover_url;
        img.style.display = 'block';
        // document.getElementById('coverBtn').removeAttribute('hidden');
        // document.getElementById('coverLabel').setAttribute('hidden', 'hidden');
        document.getElementById('oldCover').style.display = 'none';
        document.getElementById('coverInput').setAttribute('hidden', 'hidden');
    }
});
</script>
@endpush