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
    protected $fillable = ['login', 'email', 'password'];
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
}
