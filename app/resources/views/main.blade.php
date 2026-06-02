<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   @include('partials.head')

   @stack('styles')
</head>
<body>
   @if(!request()->routeIs('login.page', 'register.page'))
        @include('partials.navbar')
    @endif
   <div class="container">@yield('content')</div>

   @stack('scripts')
</body>
</html>