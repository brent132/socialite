<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    protected $imageService;
    protected $cacheTtl;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->cacheTtl = config('cache.ttl.profile_stats', 300); // 5 minutes default
    }

    /**
     * Get profile statistics with caching
     *
     * @param User $user
     * @return array
     */
    public function getProfileStats(User $user)
    {
        $cacheKey = "user.{$user->id}.profile.stats";
        
        return Cache::remember($cacheKey, now()->addSeconds($this->cacheTtl), function () use ($user) {
            // Execute a single query to get all counts at once
            $counts = DB::table('users')
                ->select([
                    DB::raw("(SELECT COUNT(*) FROM posts WHERE user_id = {$user->id}) as post_count"),
                    DB::raw("(SELECT COUNT(*) FROM follows WHERE followed_id = {$user->id}) as follower_count"),
                    DB::raw("(SELECT COUNT(*) FROM follows WHERE user_id = {$user->id}) as following_count")
                ])
                ->first();
                
            return [
                'postCount' => $counts->post_count ?? 0,
                'followerCount' => $counts->follower_count ?? 0,
                'followingCount' => $counts->following_count ?? 0
            ];
        });
    }

    /**
     * Update profile picture
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return array
     */
    public function updateProfilePicture(User $user, $image = null)
    {
        if (!$image) {
            return [
                'success' => false,
                'message' => 'No image provided'
            ];
        }
        
        // Clear user cache when updating profile
        $this->clearUserCache($user->id);

        $result = $this->imageService->processProfileImage($image, $user->profile->image);
        
        if ($result['success']) {
            $user->profile->update([
                'image' => $result['path']
            ]);
        }
        
        return $result;
    }

    /**
     * Update profile background
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile|null $image
     * @return array
     */
    public function updateProfileBackground(User $user, $image = null)
    {
        if (!$image) {
            return [
                'success' => false,
                'message' => 'No image provided'
            ];
        }

        // Clear user cache when updating profile
        $this->clearUserCache($user->id);
        
        $result = $this->imageService->processBackgroundImage($image, $user->profile->background);
        
        if ($result['success']) {
            $user->profile->update([
                'background' => $result['path']
            ]);
        }
        
        return $result;
    }

    /**
     * Remove profile background
     *
     * @param User $user
     * @return array
     */
    public function removeProfileBackground(User $user)
    {
        if ($user->profile->background) {
            $this->imageService->removeImage($user->profile->background);
            
            // Clear user cache when updating profile
            $this->clearUserCache($user->id);
            
            $user->profile->update(['background' => null]);
            
            return [
                'success' => true,
                'message' => 'Background removed successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'No background to remove'
        ];
    }

    /**
     * Update profile bio
     *
     * @param User $user
     * @param string $description
     * @return bool
     */
    public function updateProfileBio(User $user, $description)
    {
        // Clear user cache when updating profile
        $this->clearUserCache($user->id);
        
        return $user->profile->update([
            'description' => $description
        ]);
    }
    
    /**
     * Clear cache keys related to a user
     *
     * @param int $userId
     * @return void
     */
    protected function clearUserCache($userId)
    {
        $cacheKeys = [
            "user.{$userId}.profile.stats",
            "user.{$userId}",
            "user.{$userId}.profile",
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
} 