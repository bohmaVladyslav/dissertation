<nav class="navbar bg-body-tertiary border-bottom mb-4">
    <div class="container d-flex justify-content-between align-items-center">

        {{-- Левая часть --}}
        <div class="d-flex align-items-center gap-4 flex-grow-1">

            {{-- Лого --}}
            <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="#">
                <img src="{{ asset('images/book.svg') }}"
                    alt="Logo"
                    width="40"
                    height="40">

                <span class="fw-bold">MyLibrary</span>
            </a>

            {{-- Поиск --}}
            <form class="flex-grow-1" role="search">
                <input class="form-control"
                    type="search"
                    placeholder="Поиск книг...">
            </form>

        </div>

        {{-- Правая часть --}}
        <div class="ms-4 dropdown">
            <a class="d-flex align-items-center gap-2 text-decoration-none text-dark dropdown-toggle"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">

                <img src="{{ asset('images/user.png') }}"
                    class="rounded-circle"
                    width="40"
                    height="40"
                    style="object-fit: cover;">

                <span class="fw-semibold">John Doe</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('user.index') }}">
                        Профиль
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('books.create') }}">
                        Добавить книгу
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('collections.create') }}">
                        Добавить коллекцию
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            Выйти
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>