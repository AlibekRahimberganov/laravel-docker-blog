<?php

namespace App\Services;

use App\Models\Reaction;
use Illuminate\Support\Facades\Cache;

class ReactionBatchService
{
    private const INDEX_KEY = 'reaction_pending_index';
    private const LOCK_KEY = 'reaction_pending_index_lock';
    private const TYPES = ['like', 'dislike', 'recommend'];
    private const PENDING_TTL_HOURS = 6;

    /**
     * Toggle a reaction in the pending cache queue and return the resulting
     * effective state (not yet written to the reactions table).
     */
    public function toggle(int $userId, int $postId, string $type): array
    {
        $state = $this->effectiveState($userId, $postId);

        $state[$type] = !$state[$type];
        if ($type === 'like' && $state['like']) {
            $state['dislike'] = false;
        }
        if ($type === 'dislike' && $state['dislike']) {
            $state['like'] = false;
        }

        Cache::put($this->stateKey($userId, $postId), $state, now()->addHours(self::PENDING_TTL_HOURS));
        $this->markPending($userId, $postId);

        return $state;
    }

    /**
     * Current desired state for a user+post, overlaying any pending change on the DB state.
     */
    public function effectiveState(int $userId, int $postId): array
    {
        $pending = Cache::get($this->stateKey($userId, $postId));
        if ($pending !== null) {
            return $pending;
        }

        $state = [];
        foreach (self::TYPES as $type) {
            $state[$type] = Reaction::where('user_id', $userId)
                ->where('post_id', $postId)
                ->where('type', $type)
                ->exists();
        }

        return $state;
    }

    /**
     * Write every pending reaction change to the database in one pass.
     * Scheduled to run every minute (see routes/console.php) instead of
     * hitting the reactions table on every click.
     */
    public function flushAll(): void
    {
        $index = Cache::lock(self::LOCK_KEY, 5)->block(3, function () {
            $index = Cache::get(self::INDEX_KEY, []);
            Cache::forget(self::INDEX_KEY);
            return $index;
        });

        foreach (array_keys($index ?? []) as $entry) {
            [$userId, $postId] = array_map('intval', explode(':', $entry));

            $stateKey = $this->stateKey($userId, $postId);
            $state = Cache::get($stateKey);
            if ($state === null) {
                continue;
            }

            foreach (self::TYPES as $type) {
                $exists = Reaction::where('user_id', $userId)
                    ->where('post_id', $postId)
                    ->where('type', $type)
                    ->exists();

                if ($state[$type] && !$exists) {
                    Reaction::create(['user_id' => $userId, 'post_id' => $postId, 'type' => $type]);
                } elseif (!$state[$type] && $exists) {
                    Reaction::where('user_id', $userId)
                        ->where('post_id', $postId)
                        ->where('type', $type)
                        ->delete();
                }
            }

            Cache::forget($stateKey);
        }
    }

    private function markPending(int $userId, int $postId): void
    {
        Cache::lock(self::LOCK_KEY, 5)->block(3, function () use ($userId, $postId) {
            $index = Cache::get(self::INDEX_KEY, []);
            $index["{$userId}:{$postId}"] = true;
            Cache::put(self::INDEX_KEY, $index, now()->addHours(self::PENDING_TTL_HOURS));
        });
    }

    private function stateKey(int $userId, int $postId): string
    {
        return "reaction_pending:{$userId}:{$postId}";
    }
}
