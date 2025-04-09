<?php

/**
 * This is a simple script to verify our seeded data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = [
    'users',
    'profiles',
    'posts',
    'comments',
    'likes',
    'comment_likes',
    'follows'
];

echo "Database Seeded Data Summary:\n";
echo "=============================\n";

foreach ($tables as $table) {
    $count = DB::table($table)->count();
    echo "{$table}: {$count} records\n";
}

echo "\nUser Details:\n";
echo "=============\n";
$users = DB::table('users')->get(['id', 'name', 'username', 'email']);
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Username: {$user->username}, Email: {$user->email}\n";
}

echo "\nPost Details:\n";
echo "=============\n";
$posts = DB::table('posts')->take(5)->get(['id', 'user_id', 'caption', 'image', 'created_at']);
foreach ($posts as $post) {
    echo "ID: {$post->id}, User ID: {$post->user_id}, Caption: " . substr($post->caption, 0, 30) . "..., Image: {$post->image}\n";
} 