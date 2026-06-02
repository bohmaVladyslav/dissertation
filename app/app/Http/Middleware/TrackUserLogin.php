<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackUserLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = $request->user();

            // первый вход
            if (!$user->first_login_at) {
                $user->first_login_at = now();
            }

            // последний вход
            // $user->last_login_at = now();

            $user = Auth::user();
        }


        return $next($request);
    }
}