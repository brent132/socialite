<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class FollowController extends Controller
{
    public function follow($id)
    {
        $user = Auth::user();

        if ($user->id !== $id) {
            $user->following()->attach($id);

            // Clear cache for both users
            $this->clearUserCache($user->id);
            $this->clearUserCache($id);
        }

        // Check if request expects JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'following' => true
            ]);
        }

        return redirect()->back();
    }

    public function unfollow($id)
    {
        $user = Auth::user();
        $user->following()->detach($id);

        // Clear cache for both users
        $this->clearUserCache($user->id);
        $this->clearUserCache($id);

        // Check if request expects JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'following' => false
            ]);
        }

        return redirect()->back();
    }

    private function clearUserCache($userId)
    {
        Cache::forget('count.followers.' . $userId);
        Cache::forget('count.following.' . $userId);
    }

    public function followingPage(User $user)
    {
        $following = $user->following;

        $notFollowing = User::whereNotIn('id', $user->following->pluck('id'))
            ->where('id', '!=', $user->id)
            ->get();

        return view('profiles.following', compact('user', 'following', 'notFollowing'));
    }
}
