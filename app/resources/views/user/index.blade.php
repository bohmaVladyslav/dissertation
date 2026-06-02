@extends('main')
@section('content')
    <div class="row mb-5">
        <div class="d-flex col col-auto">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}" width="120" height="120" class="img-fluid rounded-circle">
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
            <h4 class="mb-0">Мои книги</h4>
            {{-- <a href="#" class="text-decoration-none">Смотреть все</a> --}}
        </div>

        <div id="myBooksCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <div class="row g-3">

                        @for($i = 0; $i < 4; $i++)
                            @if(isset($books[$i]))
                            <a href="/books/{{ $books[$i]->id }}" class="col-md-3 text-decoration-none text-dark">
                                <div class="card h-100 shadow-sm">
                                    <img src="{{ asset('storage/' . $books[$i]->cover_path) }}"
                                         class="card-img-top"
                                         style="height: 320px; object-fit: cover;">

                                    <div class="card-body">
                                        <h6 class="card-title mb-1">{{ $books[$i]->title }}</h6>
                                        <small class="text-muted">{{ $books[$i]->author }}</small>
                                    </div>
                                </div>
                            </a>
                            @endif
                        @endfor

                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row g-3">

                        @foreach($books as $book)
                            <a href="/books/{{ $book->id }}" class="col-md-3 text-decoration-none text-dark">
                                <div class="card h-100 shadow-sm">
                                    <img src="{{ asset('storage/' . $book->cover_path) }}"
                                         class="card-img-top"
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

            </div>

            <button class="carousel-control-prev"
                    type="button"
                    data-bs-target="#myBooksCarousel"
                    data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next"
                    type="button"
                    data-bs-target="#myBooksCarousel"
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
                        {{$collection->name}}
                    </h4>
                </a>
            </div>

            <div id="myBooksCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <div class="carousel-item active">
                        <div class="row g-3">

                            @for($i = 0; $i < 4; $i++)
                                @if(isset($collection->books[$i]))
                                <a href="/books/{{ $books[$i]->id }}" class="col-md-3 text-decoration-none text-dark">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ asset('storage/' . $collection->books[$i]->cover_path) }}"
                                            class="card-img-top"
                                            style="height: 320px; object-fit: cover;">

                                        <div class="card-body">
                                            <h6 class="card-title mb-1">{{ $collection->books[$i]->title }}</h6>
                                            <small class="text-muted">{{ $collection->books[$i]->author }}</small>
                                        </div>
                                    </div>
                                </a>
                                @endif
                            @endfor

                        </div>
                    </div>

                    <div class="carousel-item">
                        <div class="row g-3">

                            @foreach($collection->books as $book)
                                <a href="/books/{{ $book->id }}" class="col-md-3 text-decoration-none text-dark">
                                    <div class="card h-100 shadow-sm">
                                        <img src="{{ asset('storage/' . $book->cover_path) }}"
                                            class="card-img-top"
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

                </div>

                <button class="carousel-control-prev"
                        type="button"
                        data-bs-target="#myBooksCarousel"
                        data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>

                <button class="carousel-control-next"
                        type="button"
                        data-bs-target="#myBooksCarousel"
                        data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    @endforeach

    {{-- Любимые --}}
    {{-- <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Любимые</h4>
            <a href="#" class="text-decoration-none">Смотреть все</a>
        </div>

        <div class="row g-3">

            @for($i = 0; $i < 4; $i++)
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-warning">
                        <img src="https://picsum.photos/300/400?random=fav{{ $i }}"
                             class="card-img-top"
                             style="height: 320px; object-fit: cover;">

                        <div class="card-body">
                            <h6 class="card-title mb-1">Любимая книга</h6>
                            <small class="text-muted">Автор книги</small>
                        </div>
                    </div>
                </div>
            @endfor

        </div>
    </div> --}}

    {{-- Недавно прочитанные --}}
    {{-- <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Недавно прочитанные</h4>
            <a href="#" class="text-decoration-none">Смотреть все</a>
        </div>

        <div class="row g-3">

            @for($i = 0; $i < 6; $i++)
                <div class="col-md-2">
                    <div class="card h-100 shadow-sm">
                        <img src="https://picsum.photos/250/350?random=recent{{ $i }}"
                             class="card-img-top"
                             style="height: 260px; object-fit: cover;">

                        <div class="card-body">
                            <h6 class="card-title fs-6 mb-1">Книга</h6>
                            <small class="text-muted">Автор</small>
                        </div>
                    </div>
                </div>
            @endfor

        </div>
    </div> --}}
@endsection