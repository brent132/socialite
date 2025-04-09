@extends('layouts.app')

@section('content')
<div class="container max-w-[1000px] mx-auto px-4 py-8">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="relative h-48">
            @if($user->profile->backgroundImage())
            <!-- Background Image -->
            <img src="{{ $user->profile->backgroundImage() }}"
                alt="Profile background"
                class="w-full h-full object-cover">
            @else
            <!-- Gradient Background -->
            <div class="w-full h-full bg-gradient-to-r from-blue-400 to-purple-500"></div>
            @endif

            <!-- Profile Picture -->
            <div class="absolute -bottom-16 left-8">
                <div class="w-32 h-32 md:w-48 md:h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-white dark:border-slate-700 bg-white dark:bg-slate-800">
                    <img src="{{ $user->profile->profileImage() }}"
                        alt="{{ $user->username }}'s profile"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="pt-20 px-8 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $user->username }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ $user->name }}</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                    @can('update', $user->profile)
                    <a href="/profile/{{ $user->id }}/edit" 
                        class="px-6 py-2 border border-gray-300 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Edit Profile
                    </a>
                    <a href="/p/create" 
                        class="px-6 py-2 bg-indigo-600 dark:bg-indigo-700 rounded-xl text-white font-medium hover:bg-indigo-700 dark:hover:bg-indigo-800 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Add Post
                    </a>
                    @else
                    <follow-button user-id="{{ $user->id }}" follows="{{ $follows }}"></follow-button>
                    <a href="/chat/{{ $user->id }}" 
                        class="px-6 py-2 border border-gray-300 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Message
                    </a>
                    @endcan
                </div>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:space-x-8 mb-6">
                <div class="flex space-x-6">
                    <div class="profile-stat">
                        <span class="profile-stat-number">{{ $postCount }}</span>
                        <span class="profile-stat-label">posts</span>
                    </div>
                    <div class="profile-stat">
                        <span class="profile-stat-number">{{ $followerCount }}</span>
                        <span class="profile-stat-label">followers</span>
                    </div>
                    <div class="profile-stat">
                        <span class="profile-stat-number">{{ $followingCount }}</span>
                        <span class="profile-stat-label">following</span>
                    </div>
                </div>
            </div>

            @if($user->profile->title || $user->profile->description || $user->profile->url)
            <div class="mt-4 md:mt-8 max-w-2xl text-gray-700 dark:text-gray-300">
                @if($user->profile->title)
                <p class="font-bold text-gray-900 dark:text-white">{{ $user->profile->title }}</p>
                @endif

                @if($user->profile->description)
                <p class="mt-1">{{ $user->profile->description }}</p>
                @endif

                @if($user->profile->url)
                <a href="{{ $user->profile->url }}" class="text-blue-600 dark:text-blue-400 mt-1 block" target="_blank">{{ $user->profile->url }}</a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Posts Section -->
    <div class="mt-12">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Posts</h2>

        @if($user->posts && $user->posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($user->posts as $post)
            <div class="relative group card-hover overflow-hidden rounded-xl" x-data="{
                likeCount: {{ $post->likes ? $post->likes->count() : 0 }},
                isLiked: {{ $post->likes && $post->likes->where('user_id', auth()->id())->count() > 0 ? 'true' : 'false' }},
                toggleLike() {
                    fetch('/p/{{ $post->id }}/like', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.likeCount = data.count;
                        this.isLiked = data.liked;
                    });
                }
            }">
                <a href="/p/{{ $post->id }}">
                    <div class="aspect-square bg-gray-100 dark:bg-slate-900 overflow-hidden">
                        <img 
                            src="/storage/{{ $post->image }}" 
                            class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105" 
                            loading="lazy"
                            alt="Post by {{ $post->user->username }}"
                            onerror="this.onerror=null; this.src='/images/post-icon.svg'; this.classList.remove('object-cover'); this.classList.add('object-contain', 'p-12');">
                    </div>
                </a>

                <!-- Hover Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-6">
                    <!-- Like Button -->
                    <button @click.prevent="toggleLike" class="flex items-center space-x-1 text-white hover:text-red-500 transition-colors">
                        <svg x-show="!isLiked" class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <svg x-show="isLiked" class="w-8 h-8 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xl font-bold" x-text="likeCount"></span>
                    </button>

                    <!-- Comment Count -->
                    <div class="flex items-center space-x-1 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span class="text-xl font-bold">{{ $post->comments->count() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-gray-50 dark:bg-slate-700 p-12 rounded-xl text-center">
            <div class="w-24 h-24 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">No Posts Yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">When {{ $user->username }} shares photos, they'll appear here.</p>
            
            @can('update', $user->profile)
            <a href="/p/create" class="px-6 py-3 bg-indigo-600 dark:bg-indigo-700 rounded-xl text-white font-medium hover:bg-indigo-700 dark:hover:bg-indigo-800 transition-all">
                Create Your First Post
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection