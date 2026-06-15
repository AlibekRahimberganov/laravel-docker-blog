<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function toggle(Request $request, Posts $post, string $type)
    {
        if (!in_array($type, ['like', 'dislike', 'recommend'])) {
            abort(400);
        }

        $userId = Auth::id();

        if ($type === 'like') {
            // Remove dislike if it exists
            Reaction::where('user_id', $userId)
                ->where('post_id', $post->id)
                ->where('type', 'dislike')
                ->delete();
        } elseif ($type === 'dislike') {
            // Remove like if it exists
            Reaction::where('user_id', $userId)
                ->where('post_id', $post->id)
                ->where('type', 'like')
                ->delete();
        }

        // Toggle the requested reaction
        $existing = Reaction::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->where('type', $type)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Reaction::create([
                'user_id' => $userId,
                'post_id' => $post->id,
                'type' => $type
            ]);
        }

        return back();
    }
}
