<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to posts table
        Schema::table('posts', function (Blueprint $table) {
            // Add index for created_at for faster sorting
            $table->index('created_at');
        });

        // Add indexes to likes table
        Schema::table('likes', function (Blueprint $table) {
            // Add index for post_id for faster lookups
            if (!Schema::hasIndex('likes', 'likes_post_id_index')) {
                $table->index('post_id');
            }
        });

        // Add indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            // Add index for post_id for faster lookups
            if (!Schema::hasIndex('comments', 'comments_post_id_index')) {
                $table->index('post_id');
            }
            // Add index for created_at for faster sorting
            $table->index('created_at');
        });

        // Add indexes to follows table
        Schema::table('follows', function (Blueprint $table) {
            // Add index for followed_id for faster lookups
            if (!Schema::hasIndex('follows', 'follows_followed_id_index')) {
                $table->index('followed_id');
            }
            // Add unique index for user_id and followed_id to prevent duplicate follows
            if (!Schema::hasIndex('follows', 'follows_user_id_followed_id_unique')) {
                $table->unique(['user_id', 'followed_id']);
            }
        });

        // Add indexes to messages table
        Schema::table('messages', function (Blueprint $table) {
            // Add index for sender_id and receiver_id for faster lookups
            if (!Schema::hasIndex('messages', 'messages_sender_id_receiver_id_index')) {
                $table->index(['sender_id', 'receiver_id']);
            }
            // Add index for created_at for faster sorting
            $table->index('created_at');
            // Add index for is_read for faster filtering
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from likes table
        Schema::table('likes', function (Blueprint $table) {
            if (Schema::hasIndex('likes', 'likes_post_id_index')) {
                $table->dropIndex(['post_id']);
            }
        });

        // Remove indexes from comments table
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasIndex('comments', 'comments_post_id_index')) {
                $table->dropIndex(['post_id']);
            }
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from follows table
        Schema::table('follows', function (Blueprint $table) {
            if (Schema::hasIndex('follows', 'follows_followed_id_index')) {
                $table->dropIndex(['followed_id']);
            }
            if (Schema::hasIndex('follows', 'follows_user_id_followed_id_unique')) {
                $table->dropUnique(['user_id', 'followed_id']);
            }
        });

        // Remove indexes from messages table
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasIndex('messages', 'messages_sender_id_receiver_id_index')) {
                $table->dropIndex(['sender_id', 'receiver_id']);
            }
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_read']);
        });
    }
};
