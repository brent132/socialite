<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FollowService;
use App\DTOs\ApiResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    protected $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
        $this->middleware('auth');
    }

    public function follow($id)
    {
        $user = Auth::user();
        $success = $this->followService->followUser($user, $id);

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json($success 
                ? ApiResponse::success(['following' => true], 'Successfully followed user')
                : ApiResponse::error('Unable to follow user')
            );
        }

        return redirect()->back();
    }

    public function unfollow($id)
    {
        $user = Auth::user();
        $this->followService->unfollowUser($user, $id);

        // Check if request is AJAX
        if (request()->ajax()) {
            return response()->json(ApiResponse::success(['following' => false], 'Successfully unfollowed user'));
        }

        return redirect()->back();
    }

    public function followingPage(User $user)
    {
        $following = $user->following;
        $notFollowing = $this->followService->getUsersNotFollowing($user, 10);

        return view('profiles.following', compact('user', 'following', 'notFollowing'));
    }
}
