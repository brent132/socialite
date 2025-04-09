<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class FollowService
{
    /**
     * Follow a user
     * 
     * @param User $user
     * @param int $targetUserId
     * @return bool
     */
    public function followUser(User $user, $targetUserId)
    {
        // Don't allow following yourself
        if ($user->id === $targetUserId) {
            return false;
        }
        
        $user->following()->attach($targetUserId);
        
        // Clear cache for both users
        $this->clearUserCache($user->id);
        $this->clearUserCache($targetUserId);
        
        return true;
    }
    
    /**
     * Unfollow a user
     * 
     * @param User $user
     * @param int $targetUserId
     * @return bool
     */
    public function unfollowUser(User $user, $targetUserId)
    {
        $user->following()->detach($targetUserId);
        
        // Clear cache for both users
        $this->clearUserCache($user->id);
        $this->clearUserCache($targetUserId);
        
        return true;
    }
    
    /**
     * Clear cache related to user followers/following counts
     * 
     * @param int $userId
     * @return void
     */
    public function clearUserCache($userId)
    {
        Cache::forget('count.followers.' . $userId);
        Cache::forget('count.following.' . $userId);
    }
    
    /**
     * Get users the given user is not following
     * 
     * @param User $user
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersNotFollowing(User $user, $limit = 10)
    {
        return User::whereNotIn('id', $user->following->pluck('id'))
            ->where('id', '!=', $user->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
} 