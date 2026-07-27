<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(Request $request, User $user)
    {
        $follower = $request->user();

        if ($follower->id === $user->id) {
            abort(403);
        }

        $follower->following()->syncWithoutDetaching([$user->id]);

        return back()->with('success', "You're now following {$user->login}.");
    }

    public function destroy(Request $request, User $user)
    {
        $request->user()->following()->detach($user->id);

        return back()->with('success', "Unfollowed {$user->login}.");
    }
}
