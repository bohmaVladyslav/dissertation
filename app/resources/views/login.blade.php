@extends('main')
@section('content')
    <div class="container mx-auto text-center vh-100 d-flex flex-column align-items-center justify-content-center">
        <div class="row">
            <div class="d-flex gap-3">
                <img src="{{ asset('images/book.svg') }}" class="logo img-fluid">
                <h2>E-Reader</h1>
            </div>
        </div>
        <div class="row card p-3 col-md-4 col-sm-12 col-xs-12">
            <form class="text-left" method="post" action="{{ route('login') }}">
                @csrf
                <div class="mb-4 text-center">
                    <h4 class="card-title">Login into your account</h4>
            </div>
                <div class="mb-3">
                    <label for="email" class="form-label" required>Email address</label>
                    <input type="email" name="email" required class="form-control @if ($errors->has('email')) is-invalid @endif" id="email" placeholder="name@example.com" value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>                        
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Your password</label>
                    <input type="password" name="password" required class="form-control @if ($errors->has('password')) is-invalid @endif" id="password" placeholder="*********" value="">
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>                        
                    @enderror
                </div>
                <div class="mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-outline-dark col-12">Sign up</button>
                </div>
            </form>
        </div>
@endsection