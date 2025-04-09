<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        parent::booted();

        static::created(function ($user) {
            $user->profile()->create([]);
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    protected $with = ['profile']; // This ensures profile is always loaded with user

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'user_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'followed_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
