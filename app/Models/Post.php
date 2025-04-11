<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Define which relationships to always load
    protected $with = ['user.profile'];

    // Define casts for better type handling
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Check if a post is liked by a user
     *
     * @param User $user
     * @return bool
     */
    public function likedBy(User $user)
    {
        if (!$user) {
            return false;
        }

        // Cache the result to avoid repeated database queries
        $cacheKey = 'post_' . $this->id . '_liked_by_' . $user->id;

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            return $this->likes()->where('user_id', $user->id)->exists();
        });
    }

    /**
     * Get the like count for this post
     *
     * @return int
     */
    public function getLikeCountAttribute()
    {
        $cacheKey = 'post_' . $this->id . '_like_count';

        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return $this->likes()->count();
        });
    }

    /**
     * Get the comment count for this post
     *
     * @return int
     */
    public function getCommentCountAttribute()
    {
        $cacheKey = 'post_' . $this->id . '_comment_count';

        return Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return $this->comments()->count();
        });
    }
}
