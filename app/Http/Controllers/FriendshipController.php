<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function store(Request $request, User $user)
    {
        $sender = $request->user();

        if ($sender->id === $user->id) {
            abort(403);
        }
        if ($sender->friendshipWith($user)) {
            return back()->with('error', 'A friend request already exists between you two.');
        }

        Friendship::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Friend request sent!');
    }

    public function accept(Request $request, Friendship $friendship)
    {
        if ($request->user()->id !== $friendship->receiver_id) {
            abort(403);
        }

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Friend request accepted!');
    }

    public function destroy(Request $request, Friendship $friendship)
    {
        $userId = $request->user()->id;
        if ($userId !== $friendship->sender_id && $userId !== $friendship->receiver_id) {
            abort(403);
        }

        $friendship->delete();

        return back()->with('success', 'Done.');
    }
}
