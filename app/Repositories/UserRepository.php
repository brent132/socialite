<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    /**
     * Get a user with related data
     * 
     * @param int $userId
     * @return User
     */
    public function getUserWithRelations($userId)
    {
        $cacheKey = "user.{$userId}.with_relations";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function() use ($userId) {
            // Optimize query with specific columns and chunk loading for posts
            return User::with([
                'profile',
                'posts' => function($query) {
                    $query->latest()->select(['id', 'user_id', 'caption', 'image', 'created_at']);
                },
                'posts.likes' => function($query) {
                    $query->select(['id', 'post_id', 'user_id']);
                },
                'posts.comments' => function($query) {
                    $query->latest()->take(5)->select(['id', 'post_id', 'user_id', 'comment', 'created_at']);
                },
                'followers' => function($query) {
                    $query->select(['users.id', 'username', 'name']);
                },
                'following' => function($query) {
                    $query->select(['users.id', 'username', 'name']);
                }
            ])
            ->findOrFail($userId);
        });
    }

    /**
     * Find a user by username
     * 
     * @param string $username
     * @return User|null
     */
    public function findByUsername($username)
    {
        $cacheKey = "user.username.{$username}";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($username) {
            return User::where('username', $username)
                ->with('profile')
                ->first();
        });
    }

    /**
     * Check if a user is following another user
     * 
     * @param int $userId
     * @param int $targetUserId
     * @return bool
     */
    public function isFollowing($userId, $targetUserId)
    {
        $cacheKey = "user.{$userId}.follows.{$targetUserId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function() use ($userId, $targetUserId) {
            // Use a more efficient query that doesn't load all follows
            return DB::table('follows')
                ->where('user_id', $userId)
                ->where('followed_id', $targetUserId)
                ->exists();
        });
    }

    /**
     * Get a user's followers
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFollowers($userId, $limit = 10)
    {
        $cacheKey = "user.{$userId}.followers.{$limit}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function() use ($userId, $limit) {
            return DB::table('users')
                ->join('follows', 'users.id', '=', 'follows.user_id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where('follows.followed_id', $userId)
                ->select('users.id', 'users.username', 'users.name', 'profiles.image')
                ->orderBy('follows.created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get users a user is following
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFollowing($userId, $limit = 10)
    {
        $cacheKey = "user.{$userId}.following.{$limit}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function() use ($userId, $limit) {
            return DB::table('users')
                ->join('follows', 'users.id', '=', 'follows.followed_id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->where('follows.user_id', $userId)
                ->select('users.id', 'users.username', 'users.name', 'profiles.image')
                ->orderBy('follows.created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }
    
    /**
     * Clear user-related caches
     * 
     * @param int $userId
     * @return void
     */
    public function clearUserCache($userId)
    {
        Cache::forget("user.{$userId}.with_relations");
        Cache::forget("user.{$userId}.followers.10");
        Cache::forget("user.{$userId}.following.10");
        
        // Also clear any other caches that might have this user's data
        $userObj = User::find($userId);
        if ($userObj) {
            Cache::forget("user.username.{$userObj->username}");
        }
    }
} 