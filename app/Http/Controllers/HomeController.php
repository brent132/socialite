<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::id();

        // Get IDs of users the current user is following
        $followingIds = Cache::remember('following.ids.' . $userId, now()->addMinutes(5), function () use ($userId) {
            return DB::table('follows')
                ->where('user_id', $userId)
                ->pluck('followed_id');
        });

        // Add the user's own posts to the feed
        $followingIds->push($userId);

        // Eager load relationships to avoid N+1 queries
        $posts = Post::whereIn('user_id', $followingIds)
            ->with(['user.profile', 'likes', 'comments' => function ($query) {
                $query->latest()->limit(3)->with('user.profile');
            }])
            ->latest()
            ->paginate(5);

        return view('home', compact('posts'));
    }
}
