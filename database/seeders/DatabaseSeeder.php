<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample post images directory
        if (!file_exists(storage_path('app/public/posts'))) {
            Storage::makeDirectory('public/posts');
        }

        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
        ]);

        // Create 5 regular users
        $users = User::factory()->count(5)->create();
        $allUsers = $users->concat([$admin]);

        // Create 10 posts with direct database insertion to control the image paths
        foreach ($allUsers as $user) {
            for ($i = 0; $i < 2; $i++) {
                $post = Post::create([
                    'user_id' => $user->id,
                    'caption' => fake()->sentence(10),
                    'image' => 'posts/sample-post.jpg',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now(),
                ]);

                // Add comments to each post from random users
                $commenters = $allUsers->random(rand(1, 3));
                foreach ($commenters as $commenter) {
                    Comment::create([
                        'user_id' => $commenter->id,
                        'post_id' => $post->id,
                        'comment' => fake()->sentences(rand(1, 3), true),
                        'created_at' => now()->subDays(rand(1, 15)),
                        'updated_at' => now(),
                    ]);
                }

                // Add likes to each post from random users
                $likers = $allUsers->random(rand(2, 5));
                foreach ($likers as $liker) {
                    Like::create([
                        'user_id' => $liker->id,
                        'post_id' => $post->id,
                        'created_at' => now()->subDays(rand(1, 15)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Create follow relationships between users
        foreach ($allUsers as $user) {
            // Each user follows 2-4 random users
            $followings = $allUsers->except($user->id)->random(rand(2, 4));
            
            foreach ($followings as $followedUser) {
                DB::table('follows')->insert([
                    'user_id' => $user->id,
                    'followed_id' => $followedUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add likes to some comments
        $comments = Comment::all();
        foreach ($comments as $comment) {
            // Add 0-2 likes to each comment
            $randomUsers = $allUsers->random(rand(0, 2));
            
            foreach ($randomUsers as $user) {
                CommentLike::create([
                    'user_id' => $user->id,
                    'comment_id' => $comment->id,
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
