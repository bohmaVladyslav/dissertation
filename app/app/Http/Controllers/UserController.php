<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
         

        return view('user.index', [
            'books' => Book::all(),
            'collections' => Collection::all(),
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        return view('user.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($request->user()->id),
            ],
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|confirmed',
        ]);

        if ($request->filled('password') && $request->filled('password_confirmation')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update($validated);

        return redirect()->route('user.index');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Auth::logout();
        $user->delete();

        return redirect('/');
    }
}
