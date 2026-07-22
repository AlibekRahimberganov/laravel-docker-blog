<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Services\ReactionBatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function __construct(private ReactionBatchService $batcher)
    {
    }

    public function toggle(Request $request, Posts $post, string $type)
    {
        if (!in_array($type, ['like', 'dislike', 'recommend'])) {
            abort(400);
        }

        $userId = Auth::id();
        $state = $this->batcher->toggle($userId, $post->id, $type);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'active' => $state,
                'counts' => $this->projectedCounts($post, $userId, $state),
            ]);
        }

        return back();
    }

    private function projectedCounts(Posts $post, int $userId, array $state): array
    {
        $attributes = [
            'like' => 'likes_count',
            'dislike' => 'dislikes_count',
            'recommend' => 'recommends_count',
        ];

        $counts = [];
        foreach ($attributes as $type => $attribute) {
            $base = $post->$attribute;
            $dbHasReaction = $post->hasReaction($type, $userId);

            $delta = 0;
            if ($state[$type] && !$dbHasReaction) {
                $delta = 1;
            } elseif (!$state[$type] && $dbHasReaction) {
                $delta = -1;
            }

            $counts[$attribute] = $base + $delta;
        }

        return $counts;
    }
}
