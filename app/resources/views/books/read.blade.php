@extends('main')

@section('content')

<div class="container py-4">

    <div class="reader-header mb-4">

        <div class="d-flex align-items-center gap-3" onclick="location.href = '{{ route('books.show', $book->id) }}">

            @if($book->cover_path)

                <img
                    src="{{ asset('storage/' . $book->cover_path) }}"
                    alt="{{ $book->title }}"
                    class="reader-cover">

            @endif

            <div>

                <h1 class="mb-1">{{ $book->title }}</h1>

                <p class="text-muted mb-0">
                    {{ $book->author }}
                </p>

            </div>

        </div>

    </div>

    <div class="reader-body">

        {{-- TXT --}}
        @if($type === 'txt')

            <div class="txt-reader">

                <pre>{{ $content }}</pre>

            </div>

        {{-- PDF --}}
        @elseif($type === 'pdf')

            <iframe
                src="{{ $fileUrl }}"
                class="pdf-reader">
            </iframe>

        {{-- EPUB --}}
        @elseif($type === 'epub')

            <div class="epub-controls mb-3">

                <button id="prevPage" class="btn btn-outline-primary">
                    ← Back
                </button>

                <button id="nextPage" class="btn btn-outline-primary">
                    Forward →
                </button>

                <button id="firstPage" class="btn btn-outline-secondary">
                    At the beginning
                </button>

                <div class="ms-3 d-flex gap-2 align-items-center">

                    <input
                        type="text"
                        id="goToPageInput"
                        class="form-control"
                        style="width: 120px"
                        placeholder="page">

                    <button id="goToPageBtn" class="btn btn-primary">
                        Go to
                    </button>

                </div>

            </div>

            <div id="viewer"></div>

        {{-- FB2 --}}
        @elseif($type === 'fb2')

            <div class="fb2-reader" id="fb2Reader">

                {!! $content !!}

            </div>

        @else

            <div class="alert alert-danger">
                Неподдерживаемый формат файла
            </div>

        @endif

    </div>

</div>

@endsection

@push('styles')

<style>

.reader-cover {
    width: 100px;
    height: 140px;
    object-fit: cover;
    border-radius: 10px;
}

.txt-reader {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 30px;
    max-height: 85vh;
    overflow-y: auto;
}

.txt-reader pre {
    white-space: pre-wrap;
    word-break: break-word;
    font-family: serif;
    font-size: 18px;
    line-height: 1.8;
    margin: 0;
}

.pdf-reader {
    width: 100%;
    height: 90vh;
    border: none;
    border-radius: 12px;
}

#viewer {
    width: 100%;
    height: 85vh;
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    background: white;
}

.epub-controls {
    display: flex;
    gap: 10px;
}

.fb2-reader {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 40px;
    max-height: 85vh;
    overflow-y: auto;
    font-family: serif;
    line-height: 1.8;
    font-size: 18px;
}

.fb2-reader h1,
.fb2-reader h2,
.fb2-reader h3 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.fb2-reader p {
    margin-bottom: 1rem;
    text-align: justify;
}

</style>

@endpush

@push('scripts')

@if($type === 'epub')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>

<script>

    const savedProgress = @json($book->progress?->progress);

    const book = ePub("{{ $fileUrl }}");

    book.ready.then(() => {

        const rendition = book.renderTo("viewer", {
            width: "100%",
            height: "100%"
        });

        rendition.display(savedProgress || undefined);

        document.getElementById('nextPage').addEventListener('click', () => {
            rendition.next();
        });

        document.getElementById('prevPage').addEventListener('click', () => {
            rendition.prev();
        });

        rendition.on('relocated', function(location) {

            fetch('/books/{{ $book->id }}/progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    progress: location.start.cfi
                })
            });

        });

        document.getElementById('firstPage').addEventListener('click', () => {
            rendition.display();
        });

        document.getElementById('goToPageBtn').addEventListener('click', () => {

            const value = document.getElementById('goToPageInput').value.trim();

            if (!value) return;

            // EPUB CFI
            rendition.display(value);

        });

    });

</script>

@endif

@if($type === 'fb2')

<script>

const reader = document.getElementById('fb2Reader');

const savedProgress = {{ $book->progress?->progress ?? 0 }};

console.log(savedProgress);

reader.scrollTop =
    (reader.scrollHeight - reader.clientHeight) * (savedProgress / 100);

let timeout;

reader.addEventListener('scroll', () => {

    clearTimeout(timeout);

    timeout = setTimeout(() => {

        const progress =
            reader.scrollTop /
            (reader.scrollHeight - reader.clientHeight) * 100;

        fetch('/books/{{ $book->id }}/progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                progress: '' +progress
            })
        });

    }, 500);

});

</script>

@endif

@endpush