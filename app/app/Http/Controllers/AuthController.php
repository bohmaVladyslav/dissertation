<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $user->first_login_at = now();
        $user->last_login_at = now();
        $user->save();


        return redirect('user/');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
                'password' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->first_login_at) {
            $user->first_login_at = now();
        }

        $user->last_login_at = now();
        $user->save();

        return redirect()->route('user.index');
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }


    public function logout(Request $request)
    {
        Auth::logout();

        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return redirect('/');
    }
}
