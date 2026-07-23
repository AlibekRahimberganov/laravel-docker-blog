<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['login', 'email', 'password', 'avatar', 'bio'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'password' => 'hashed',
    ];
    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id');
    }

    public function coAuthoredPosts()
    {
        return Posts::whereHas('coAuthors', fn ($q) => $q->where('user_id', $this->id));
    }

    public function visiblePosts()
    {
        return Posts::where('user_id', $this->id)
            ->orWhereHas('coAuthors', fn ($q) => $q->where('user_id', $this->id));
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : asset('images/default-avatar.svg');
    }

    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    public function friends()
    {
        $friendIds = Friendship::where('status', 'accepted')
            ->where(fn ($q) => $q->where('sender_id', $this->id)->orWhere('receiver_id', $this->id))
            ->get()
            ->map(fn ($f) => $f->sender_id === $this->id ? $f->receiver_id : $f->sender_id);

        return User::whereIn('id', $friendIds);
    }

    public function friendshipWith(User $other): ?Friendship
    {
        return Friendship::where(fn ($q) => $q->where('sender_id', $this->id)->where('receiver_id', $other->id))
            ->orWhere(fn ($q) => $q->where('sender_id', $other->id)->where('receiver_id', $this->id))
            ->first();
    }

    public function isFriendsWith(User $other): bool
    {
        return $this->friendshipWith($other)?->status === 'accepted';
    }

    public function unreadMessagesCount(): int
    {
        return Message::where('receiver_id', $this->id)->whereNull('read_at')->count();
    }
}
