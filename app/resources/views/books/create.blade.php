@extends('main')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">

        <h3 class="mb-4">Добавить книгу</h3>

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Название --}}
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control @error('title')is-invalid @endif"
                       value="{{ old('title') }}"
                       required>
                    
                    @error('title')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
            </div>

            {{-- Автор --}}
            <div class="mb-3">
                <label class="form-label">Автор</label>
                <input type="text"
                       name="author"
                       class="form-control @error('author')is-invalid @endif"
                       id="author"
                       value="{{ old('author') }}"
                       required>
                
                @error('author')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            {{-- Описание --}}
            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea name="description"
                        id="description"
                        class="form-control @error('description')is-invalid @endif"
                        rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label id="coverLabel">Обложка</label>

                <button type="button" id="coverBtn" onclick="document.getElementById('coverInput').click()">
                    Заменить обложку
                </button>

                <input type="file" name="cover" id="coverInput" class="form-control @error('cover')is-invalid @endif">

                @error('cover')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                <img id="coverPreview" style="max-width:150px; display:none; margin-top:10px;">
            </div>

            {{-- Файл книги --}}
            <div class="mb-3">
                <label class="form-label">Файл книги</label>

                <input type="file"
                    name="file"
                    class="form-control mt-2 @error('file')is-invalid @endif">

                @error('file')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
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
        console.log(data);
        document.querySelector('textarea[name="description"]').value = data.description;
    }

    if (data.cover_url) {
        const img = document.getElementById('coverPreview');
        img.src = data.cover_url;
        img.style.display = 'block';
        document.getElementById('coverBtn').removeAttribute('hidden');
        document.getElementById('coverLabel').setAttribute('hidden', 'hidden');
        document.getElementById('coverInput').setAttribute('hidden', 'hidden');
    }
});
</script>
@endpush