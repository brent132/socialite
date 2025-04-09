<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        try {
            $data = request()->validate([
                'caption' => 'required|max:2200',
                'image' => 'required|mimes:jpeg,png,jpg,gif,webp',
            ]);

            $imagePath = request('image')->store('uploads', 'public');

            $manager = new ImageManager(new Driver());
            $image = $manager->read(public_path("storage/{$imagePath}"));
            $image->scale(width: 1200, height: 1200);
            $image->save();

            auth()->user()->posts()->create([
                'caption' => $data['caption'],
                'image' => $imagePath,
            ]);

            // Clear post count cache
            Cache::forget('count.posts.' . auth()->id());

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Post created successfully']);
            }

            return redirect('/profile/' . auth()->user()->id)->with('success', 'Post created successfully');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['message' => $e->getMessage() ?: 'Failed to create post'], 422);
            }

            return back()->withErrors(['error' => 'Failed to create post'])->withInput();
        }
    }

    public function show(\App\Models\Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // Delete the image from storage
        Storage::disk('public')->delete($post->image);

        // Clear post count cache
        Cache::forget('count.posts.' . $post->user_id);

        $post->delete();

        return redirect('/profile/' . auth()->user()->id);
    }

    public function like(Post $post)
    {
        try {
            $user = auth()->user();

            if ($post->likedBy($user)) {
                $post->likes()->where('user_id', $user->id)->delete();
                $liked = false;
            } else {
                $post->likes()->create([
                    'user_id' => $user->id
                ]);
                $liked = true;
            }

            $count = $post->likes()->count();

            if (request()->expectsJson()) {
                return response()->json([
                    'liked' => $liked,
                    'count' => $count
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to process like: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to process like');
        }
    }
}
