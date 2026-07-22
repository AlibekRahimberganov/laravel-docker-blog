<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // creating an acc
        $validated = $request->validate([
            'login' => 'required|string|max:25|min:5|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()]
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        Auth::login($user);

        return redirect()->route('blog.home');
    }

    public function login(Request $request)
    {
        //log in to an existent account
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect()->route('blog.home');
        }
        throw ValidationException::withMessages([
            'credentials' => 'Incorrect credentials.',
        ]);
    }
    public function logout(Request $request)
    {
        // log out from an account
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('blog.home');
    }
    public function showregister()
    {
        return view('register');
    }
}
