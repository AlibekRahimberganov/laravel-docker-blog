<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // creating an acc
        $validated = $request->validate([
            'login' => 'required|string|max:25|min:5|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        Auth::login($user);

        return redirect()->route('blog.home');
    }

    public function login(Request $request)
    {
        // log in to an existent account
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
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

    public function editProfile(Request $request)
    {
        return view('profile-edit', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'bio' => 'nullable|string|max:1000',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
        } else {
            unset($validated['avatar']);
        }
        unset($validated['remove_avatar']);

        $user->update($validated);

        return redirect()->route('blog.profile')->with('success', 'Profile updated!');
    }
}
