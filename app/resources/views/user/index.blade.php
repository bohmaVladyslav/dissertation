@extends('main')
@section('content')
    <div class="row mb-5">
        <div class="d-flex col col-auto">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}" width="120"
                height="120" class="img-fluid rounded-circle">
        </div>
        <div class="d-flex flex-column col">
            <div class="d-flex align-items-center gap-2">
                <h3>{{ $user->name }}</h3>
                {{-- <span class="ba    dge rounded-pill text-bg-success">415 Reads</span> --}}
            </div>
            <div class="d-flex">
                <span><b class="me-1">Joined:</b>{{ $user->first_login_formatted }}</span>
            </div>
            <div class="d-flex">
                <span><b class="me-1">Last read:</b>{{ $user->last_login_formatted }}</span>
            </div>
        </div>
    </div>

    {{-- Мои книги --}}
    <div class="row mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">My books</h4>
            {{-- <a href="#" class="text-decoration-none">Смотреть все</a> --}}
        </div>

        <div id="collectionCarousel-all" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                @foreach ($booksChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row g-3">

                            @foreach ($chunk as $book)
                                <a href="/books/{{ $book->id }}" class="col-md-3 text-decoration-none text-dark">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ asset('storage/' . $book->cover_path) }}" class="card-img-top"
                                            style="height: 320px; object-fit: cover;">

                                        <div class="card-body">
                                            <h6 class="card-title mb-1">{{ $book->title }}</h6>
                                            <small class="text-muted">{{ $book->author }}</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                        </div>
                    </div>
                @endforeach

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#collectionCarousel-all"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#collectionCarousel-all"
                data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    @foreach ($collections as $collection)
        <div class="row mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('collections.show', $collection->id) }}" class="mb-0 text-decoration-none text-dark">
                    <h4 class="mb-0">
                        {{ $collection->name }}
                    </h4>
                </a>
            </div>

            <div id="collectionCarousel-{{ $collection->id }}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    @foreach ($booksChunks as $index => $chunk)
                        @php
                            $booksChunks = $collection->books->values()->chunk(4);
                        @endphp
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-3">

                                @foreach ($chunk as $book)
                                    <a href="/books/{{ $book->id }}" class="col-md-3 text-decoration-none text-dark">
                                        <div class="card h-100 shadow-sm">
                                            <img src="{{ asset('storage/' . $book->cover_path) }}" class="card-img-top"
                                                style="height: 320px; object-fit: cover;">

                                            <div class="card-body">
                                                <h6 class="card-title mb-1">{{ $book->title }}</h6>
                                                <small class="text-muted">{{ $book->author }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#collectionCarousel-{{ $collection->id }}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#collectionCarousel-{{ $collection->id }}" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    @endforeach
@endsection
