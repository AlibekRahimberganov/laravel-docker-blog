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
        'password' => 'hashed',
        'remember_token',
    ];
    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
