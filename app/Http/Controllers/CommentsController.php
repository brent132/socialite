<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $data['comment'],
        ]);

        // Load the user relationship for the response
        $comment->load('user.profile');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'user' => [
                'id' => $comment->user->id,
                'username' => $comment->user->username,
                'profile_image' => $comment->user->profile->profileImage(),
            ]
        ]);
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

        $comment->delete();

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

        // Check if the user has already liked this comment
        $liked = $comment->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            // Unlike
            $comment->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            // Like
            $comment->likes()->create([
                'user_id' => $user->id
            ]);
            $liked = true;
        }

        $count = $comment->likes()->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $count
        ]);
    }

    /**
     * Get comments for a post.
     */
    public function index(Request $request, Post $post)
    {
        $limit = $request->input('limit', 10);

        $comments = $post->comments()
            ->with('user.profile')
            ->latest()
            ->paginate($limit);

        // Add liked status for the authenticated user and profile image
        $comments->getCollection()->transform(function ($comment) {
            $comment->liked = $comment->likes()->where('user_id', Auth::id())->exists();
            $comment->likes_count = $comment->likes()->count();

            // Add profile image to user
            if ($comment->user && $comment->user->profile) {
                $comment->user->profile_image = $comment->user->profile->profileImage();
            } else {
                $comment->user->profile_image = '/storage/profile/default-avatar.png';
            }

            return $comment;
        });

        // Add total count for frontend pagination
        $response = $comments->toArray();
        $response['total'] = $post->comments()->count();

        return response()->json($response);
    }
}
