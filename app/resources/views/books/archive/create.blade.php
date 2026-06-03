@extends('main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    Upload the book archive
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
                            <label for="archive" class="form-label">Book archive (ZIP / RAR)</label>
                            <input 
                                type="file" 
                                class="form-control"
                                id="archive"
                                name="archive"
                                accept=".zip,.rar,.7z"
                                required
                            >
                            <div class="form-text">
                                Supported archive formats: ZIP, RAR, 7Z. The archives must contain books (epub, fb2, pdf, txt).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="collection_name" class="form-label">Collection title (optional)</label>
                            <input 
                                type="text"
                                class="form-control"
                                id="collection_name"
                                name="collection_name"
                                placeholder="Fantasy 2026"
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
                            Download and process the archive
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

            <div class="mt-3 text-muted small col-md-8">
                Once the download is complete, the system will automatically:
                <ul>
                    <li>unpack the archive</li>
                    <li>create a collection (if none is specified, it will be created automatically)</li>
                    <li>add the books to the database</li>
                    <li>attempt to extract metadata</li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection