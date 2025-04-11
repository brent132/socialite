<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use App\Models\User;

class ProfilesController extends Controller
{
    public function index(\App\Models\User $user)
    {
        $user->load(['posts.likes', 'posts.comments', 'followers', 'following']);

        $postCount = Cache::remember('count.posts.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->posts->count();
        });

        $followerCount = Cache::remember('count.followers.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->followers->count();
        });

        $followingCount = Cache::remember('count.following.' . $user->id, now()->addSeconds(5), function () use ($user) {
            return $user->following->count();
        });

        return view('profiles.index', compact('user', 'postCount', 'followerCount', 'followingCount'));
    }

    public function edit(\App\Models\User $user)
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
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
        $imagePath = null;

        // Handle profile image upload
        if (request()->hasFile('image')) {
            request()->validate([
                'image' => 'required|image|max:5120', // 5MB max
            ]);

            // Remove old image if exists and it's not the default
            if ($user->profile->image) {
                Storage::disk('public')->delete($user->profile->image);
            }

            $imagePath = request('image')->store('profile', 'public');

            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read(public_path("storage/{$imagePath}"));
                $image->scale(width: 400, height: 400);
                $image->save();

                $user->profile->update([
                    'image' => $imagePath
                ]);

                $updated = true;
                $responseMessage = 'Profile picture updated successfully';
            } catch (\Exception $exception) {
                Storage::disk('public')->delete($imagePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process profile image: ' . $exception->getMessage()
                ], 500);
            }
        }

        // Handle background image upload
        if (request()->hasFile('background')) {
            request()->validate([
                'background' => 'required|image|max:10240', // 10MB max
            ]);

            // Remove old background if exists
            if ($user->profile->background) {
                Storage::disk('public')->delete($user->profile->background);
            }

            $backgroundPath = request('background')->store('profile/backgrounds', 'public');

            try {
                $manager = new ImageManager(new Driver());
                $background = $manager->read(public_path("storage/{$backgroundPath}"));
                $background->scale(width: 1920, height: 1080);
                $background->save();

                $user->profile->update([
                    'background' => $backgroundPath
                ]);

                $updated = true;
                $responseMessage = $imagePath ? 'Profile images updated successfully' : 'Background updated successfully';
            } catch (\Exception $exception) {
                Storage::disk('public')->delete($backgroundPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process background image: ' . $exception->getMessage()
                ], 500);
            }
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

            // Remove old image if exists and it's not the default
            if ($user->profile->image) {
                Storage::disk('public')->delete($user->profile->image);
            }

            $imagePath = request('image')->store('profile', 'public');

            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read(public_path("storage/{$imagePath}"));
                $image->scale(width: 400, height: 400);
                $image->save();

                $user->profile->update([
                    'image' => $imagePath
                ]);

                $updated = true;
                $responseMessage = 'Profile updated successfully';
            } catch (\Exception $exception) {
                Storage::disk('public')->delete($imagePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process profile image: ' . $exception->getMessage()
                ], 500);
            }
        }

        // Handle background image upload
        if (request()->hasFile('background')) {
            request()->validate([
                'background' => 'required|image|max:10240', // 10MB max
            ]);

            // Remove old background if exists
            if ($user->profile->background) {
                Storage::disk('public')->delete($user->profile->background);
            }

            $backgroundPath = request('background')->store('profile/backgrounds', 'public');

            try {
                $manager = new ImageManager(new Driver());
                $background = $manager->read(public_path("storage/{$backgroundPath}"));
                $background->scale(width: 1920, height: 1080);
                $background->save();

                $user->profile->update([
                    'background' => $backgroundPath
                ]);

                $updated = true;
                $responseMessage = 'Profile updated successfully';
            } catch (\Exception $exception) {
                Storage::disk('public')->delete($backgroundPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process background image: ' . $exception->getMessage()
                ], 500);
            }
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
