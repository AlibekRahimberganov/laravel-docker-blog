<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    /** @use HasFactory<\Database\Factories\PostsFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'content_media',
        'content_media_artist',
        'content_media_cover',
        'category_id',
        'published_at',
        'edited_at',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function coAuthors()
    {
        return $this->hasMany(PostAuthor::class, 'post_id')->orderBy('display_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function isEditableBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->id === $this->user_id || $this->coAuthors()->where('user_id', $user->id)->exists();
    }

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'edited_at';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'post_id');
    }

    public function views()
    {
        return $this->hasMany(PostView::class, 'post_id');
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }

    public function getLikesCountAttribute()
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->reactions()->where('type', 'dislike')->count();
    }

    public function getRecommendsCountAttribute()
    {
        return $this->reactions()->where('type', 'recommend')->count();
    }

    public function hasReaction(string $type, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        if (! $userId) {
            return false;
        }

        return $this->reactions()->where('user_id', $userId)->where('type', $type)->exists();
    }
}
