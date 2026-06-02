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
            <form class="text-left" method="post" action="{{ route('register') }}">
                @csrf
                <div class="mb-4 text-center">
                    <h4 class="card-title">Sign up your account</h4>
            </div>
                <div class="mb-3">
                    <label for="name" class="form-label" required>Your name</label>
                    <input type="text" name="name" required class="form-control @if ($errors->has('name')) is-invalid @endif" id="name" placeholder="John Doe" value="{{ old('name') }}">
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>                        
                    @enderror
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
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Repeat your password</label>
                    <input type="password" name="password_confirmation" required class="form-control @if ($errors->has('password')) is-invalid @endif" id="password_confirmation" placeholder="*********" value="">
                    {{-- <div class="invalid-feedback">
                        Please provide a valid password.
                    </div> --}}
                </div>
                <div class="mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-outline-dark col-12">Sign up</button>
                </div>
            </form>
        </div>
    </div>
@endsection