<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => 'required|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $comment = $post->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $data['comment'],
            ]);

            // Load the user relationship for the response
            $comment->load('user.profile');
            
            // Invalidate cache for post comments count
            Cache::forget("post.{$post->id}.comments_count");
            
            DB::commit();

            return response()->json([
                'success' => true,
                'comment' => $comment,
                'user' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username,
                    'profile_image' => $comment->user->profile->profileImage(),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment'
            ], 500);
        }
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'comment' => 'required|max:1000',
        ]);

        $comment->update([
            'comment' => $data['comment'],
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $postId = $comment->post_id;
        $comment->delete();
        
        // Invalidate cache for post comments count
        Cache::forget("post.{$postId}.comments_count");

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Toggle like on a comment.
     */
    public function like(Comment $comment)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Using DB transaction for atomic operation
        DB::beginTransaction();
        try {
            // Optimized query: Use exists instead of loading the entire relationship
            $liked = $comment->likes()->where('user_id', $userId)->exists();

            if ($liked) {
                // Unlike - more efficient direct delete
                $comment->likes()->where('user_id', $userId)->delete();
                $liked = false;
            } else {
                // Like
                $comment->likes()->create([
                    'user_id' => $userId
                ]);
                $liked = true;
            }

            // Use count query directly rather than loading all records
            $count = $comment->likes()->count();
            
            // Invalidate cache
            Cache::forget("comment.{$comment->id}.likes_count");
            
            DB::commit();

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process like'
            ], 500);
        }
    }

    /**
     * Get comments for a post.
     */
    public function index(Request $request, Post $post)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $cacheKey = "post.{$post->id}.comments.page{$page}.limit{$limit}";
        
        // Try to get from cache first (short TTL for comments that change frequently)
        $cachedData = Cache::remember($cacheKey, now()->addMinutes(2), function() use ($post, $limit, $page) {
            // Eager load relationships to avoid N+1 queries
            $comments = $post->comments()
                ->with(['user.profile', 'likes' => function($query) {
                    $query->select('id', 'comment_id', 'user_id');
                }])
                ->latest()
                ->paginate($limit);
                
            $authId = Auth::id();
            
            // Optimize transformation by using collection methods once
            $comments->getCollection()->transform(function ($comment) use ($authId) {
                // Use the preloaded likes relation instead of running a new query for each comment
                $comment->liked = $comment->likes->where('user_id', $authId)->isNotEmpty();
                $comment->likes_count = $comment->likes->count();
                
                // Add profile image to user
                if ($comment->user && $comment->user->profile) {
                    $comment->user->profile_image = $comment->user->profile->profileImage();
                } else {
                    $comment->user->profile_image = '/storage/profile/default-avatar.png';
                }
                
                // Remove unnecessary large data from response
                unset($comment->likes);
                
                return $comment;
            });
            
            // Get total count from cache to avoid additional query
            $totalComments = Cache::remember(
                "post.{$post->id}.comments_count", 
                now()->addMinutes(5), 
                function() use ($post) {
                    return $post->comments()->count();
                }
            );
            
            $response = $comments->toArray();
            $response['total'] = $totalComments;
            
            return $response;
        });

        return response()->json($cachedData);
    }
}
