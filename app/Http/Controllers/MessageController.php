<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $user->friends()->get()->map(function (User $friend) use ($user) {
            $lastMessage = $this->threadQuery($user, $friend)->latest()->first();

            return [
                'friend' => $friend,
                'last_message' => $lastMessage,
                'unread_count' => Message::where('sender_id', $friend->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
            ];
        })->sortByDesc(fn ($conversation) => $conversation['last_message']?->created_at)->values();

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, User $user)
    {
        $current = $request->user();
        if (! $current->isFriendsWith($user)) {
            abort(403);
        }

        $messages = $this->threadQuery($current, $user)->orderBy('created_at')->get();

        Message::where('sender_id', $user->id)
            ->where('receiver_id', $current->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', ['friend' => $user, 'messages' => $messages]);
    }

    public function store(Request $request, User $user)
    {
        $current = $request->user();
        if (! $current->isFriendsWith($user)) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id' => $current->id,
            'receiver_id' => $user->id,
            'body' => $validated['body'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('messages.partials.bubble', ['message' => $message])->render(),
            ]);
        }

        return redirect()->route('messages.show', $user);
    }

    private function threadQuery(User $a, User $b)
    {
        return Message::where(fn ($q) => $q->where('sender_id', $a->id)->where('receiver_id', $b->id))
            ->orWhere(fn ($q) => $q->where('sender_id', $b->id)->where('receiver_id', $a->id));
    }
}
