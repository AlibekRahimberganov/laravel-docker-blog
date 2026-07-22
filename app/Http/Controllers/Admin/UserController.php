<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('posts')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', "You can't change your own role.");
        }

        $user->forceFill($validated)->save();

        return back()->with('success', "{$user->login}'s role updated to {$validated['role']}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', "You can't delete your own account.");
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
