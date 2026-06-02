@extends('main')

@section('content')
<div class="container">
    <h1>Редактирование профиля</h1>

    {{-- Ошибки --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- АВАТАР --}}
        <div class="mb-3">
            <label class="form-label">Аватар</label>

            <div class="mb-2">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png') }}"
                     alt="avatar"
                     width="120"
                     height="120"
                     style="object-fit: cover; border-radius: 50%;">
            </div>

            <input type="file" name="avatar" class="form-control" accept="image/*">
        </div>

        {{-- ИМЯ --}}
        <div class="mb-3">
            <label class="form-label">Имя</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   class="form-control"
                   required>
        </div>

        {{-- EMAIL --}}
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password"
                   name="password"
                   value=""
                   class="form-control"
                   >
        </div>

        <div class="mb-3">
            <label class="form-label">Password confirmation</label>
            <input type="password_confirmation"
                   name="password_confirmation"
                   value=""
                   class="form-control"
                   >
        </div>

        {{-- КНОПКА --}}
        <button type="submit" class="btn btn-primary">
            Сохранить
        </button>
    </form>
</div>
@endsection