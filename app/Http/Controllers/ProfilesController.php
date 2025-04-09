<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use App\Services\FollowService;
use App\Repositories\UserRepository;
use App\DTOs\ApiResponse;

class ProfilesController extends Controller
{
    protected $profileService;
    protected $userRepository;
    protected $followService;

    public function __construct(ProfileService $profileService, UserRepository $userRepository, FollowService $followService)
    {
        $this->profileService = $profileService;
        $this->userRepository = $userRepository;
        $this->followService = $followService;
    }

    public function index(User $user)
    {
        // Load user with relationships
        $user = $this->userRepository->getUserWithRelations($user->id);
        
        // Get profile stats with caching
        $stats = $this->profileService->getProfileStats($user);
        
        // Check if authenticated user follows this profile
        $follows = false;
        if (auth()->check()) {
            $follows = auth()->user()->following->contains($user->id);
        }
        
        return view('profiles.index', [
            'user' => $user,
            'postCount' => $stats['postCount'],
            'followerCount' => $stats['followerCount'],
            'followingCount' => $stats['followingCount'],
            'follows' => $follows
        ]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
    }

    public function updatePicture(User $user)
    {
        $this->authorize('update', $user->profile);

        // Handle background removal request
        if (request()->has('remove_background')) {
            $result = $this->profileService->removeProfileBackground($user);
            return response()->json($result);
        }

        $updated = false;
        $responseMessage = 'No changes made';

        // Handle profile image upload
        if (request()->hasFile('image')) {
            request()->validate([
                'image' => 'required|image|max:5120', // 5MB max
            ]);

            $result = $this->profileService->updateProfilePicture($user, request('image'));
            
            if ($result['success']) {
                $updated = true;
                $responseMessage = $result['message'];
            } else {
                return response()->json($result, 500);
            }
        }

        // Handle background image upload
        if (request()->hasFile('background')) {
            request()->validate([
                'background' => 'required|image|max:10240', // 10MB max
            ]);

            $result = $this->profileService->updateProfileBackground($user, request('background'));
            
            if ($result['success']) {
                $updated = true;
                $responseMessage = $updated && isset($profileResult) ? 'Profile images updated successfully' : $result['message'];
            } else {
                return response()->json($result, 500);
            }
        }

        if ($updated) {
            return response()->json(ApiResponse::success(null, $responseMessage));
        }

        return response()->json(ApiResponse::error('No image provided'), 400);
    }

    public function updateBio(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'description' => 'required|max:100',
        ]);

        $success = $this->profileService->updateProfileBio($user, $data['description']);

        if (request()->wantsJson()) {
            return response()->json(ApiResponse::success(null, 'Bio updated successfully'));
        }

        return redirect()->back()->with('success', 'Bio updated successfully');
    }
}
