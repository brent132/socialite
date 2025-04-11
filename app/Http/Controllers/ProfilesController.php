<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use App\Models\User;

class ProfilesController extends Controller
{
    public function index(User $user)
    {
        // Eager load relationships with pagination for posts to improve performance
        $user->load([
            'followers',
            'following'
        ]);

        // Get paginated posts with eager loading
        $posts = $user->posts()
            ->with(['likes', 'comments' => function ($query) {
                $query->latest()->limit(3)->with('user.profile');
            }])
            ->latest()
            ->paginate(9); // Show 9 posts per page in a 3x3 grid

        // Cache counts for better performance
        $postCount = Cache::remember('count.posts.' . $user->id, now()->addMinutes(10), function () use ($user) {
            return $user->posts()->count();
        });

        $followerCount = Cache::remember('count.followers.' . $user->id, now()->addMinutes(10), function () use ($user) {
            return $user->followers->count();
        });

        $followingCount = Cache::remember('count.following.' . $user->id, now()->addMinutes(10), function () use ($user) {
            return $user->following->count();
        });

        return view('profiles.index', compact('user', 'posts', 'postCount', 'followerCount', 'followingCount'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
    }

    /**
     * Process profile image upload
     */
    private function processProfileImage(User $user, $file)
    {
        // Remove old image if exists
        if ($user->profile->image) {
            Storage::disk('public')->delete($user->profile->image);
        }

        $imagePath = $file->store('profile', 'public');

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read(public_path("storage/{$imagePath}"));
            $image->scale(width: 400, height: 400);
            $image->save();

            $user->profile->update([
                'image' => $imagePath
            ]);

            return [
                'success' => true,
                'path' => $imagePath
            ];
        } catch (\Exception $exception) {
            Storage::disk('public')->delete($imagePath);
            return [
                'success' => false,
                'message' => 'Failed to process profile image: ' . $exception->getMessage()
            ];
        }
    }

    /**
     * Process background image upload
     */
    private function processBackgroundImage(User $user, $file)
    {
        // Remove old background if exists
        if ($user->profile->background) {
            Storage::disk('public')->delete($user->profile->background);
        }

        $backgroundPath = $file->store('profile/backgrounds', 'public');

        try {
            $manager = new ImageManager(new Driver());
            $background = $manager->read(public_path("storage/{$backgroundPath}"));
            $background->scale(width: 1920, height: 1080);
            $background->save();

            $user->profile->update([
                'background' => $backgroundPath
            ]);

            return [
                'success' => true,
                'path' => $backgroundPath
            ];
        } catch (\Exception $exception) {
            Storage::disk('public')->delete($backgroundPath);
            return [
                'success' => false,
                'message' => 'Failed to process background image: ' . $exception->getMessage()
            ];
        }
    }

    public function updatePicture(User $user)
    {
        $this->authorize('update', $user->profile);

        if (request()->has('remove_background')) {
            if ($user->profile->background) {
                Storage::disk('public')->delete($user->profile->background);
                $user->profile->update(['background' => null]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Background removed successfully'
            ]);
        }

        $updated = false;
        $responseMessage = 'No changes made';

        // Handle profile image upload
        if (request()->hasFile('image')) {
            request()->validate([
                'image' => 'required|image|max:5120', // 5MB max
            ]);

            $result = $this->processProfileImage($user, request('image'));

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            $updated = true;
            $responseMessage = 'Profile picture updated successfully';
        }

        // Handle background image upload
        if (request()->hasFile('background')) {
            request()->validate([
                'background' => 'required|image|max:10240', // 10MB max
            ]);

            $result = $this->processBackgroundImage($user, request('background'));

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            $updated = true;
            $responseMessage = $updated ? 'Profile images updated successfully' : 'Background updated successfully';
        }

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => $responseMessage
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image provided'
        ], 400);
    }

    public function updateBio(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'description' => 'required|max:100',
        ]);

        $user->profile->update($data);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Bio updated successfully');
    }

    public function update(User $user)
    {
        $this->authorize('update', $user->profile);

        $updated = false;
        $responseMessage = 'No changes made';

        // Handle bio update
        if (request()->has('description')) {
            $data = request()->validate([
                'description' => 'required|max:100',
            ]);

            $user->profile->update($data);
            $updated = true;
            $responseMessage = 'Profile updated successfully';
        }

        // Handle profile image upload
        if (request()->hasFile('image')) {
            request()->validate([
                'image' => 'required|image|max:5120', // 5MB max
            ]);

            $result = $this->processProfileImage($user, request('image'));

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            $updated = true;
            $responseMessage = 'Profile updated successfully';
        }

        // Handle background image upload
        if (request()->hasFile('background')) {
            request()->validate([
                'background' => 'required|image|max:10240', // 10MB max
            ]);

            $result = $this->processBackgroundImage($user, request('background'));

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            $updated = true;
            $responseMessage = 'Profile updated successfully';
        }

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => $responseMessage
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No changes made'
        ], 400);
    }
}
