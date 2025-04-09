@extends('layouts.app')

@section('content')
<div class="container max-w-[1000px] mx-auto px-4 py-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
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
                <div class="w-32 h-32 md:w-48 md:h-48 rounded-2xl overflow-hidden shadow-xl border-4 border-white bg-white">
                    <img src="{{ $user->profile->profileImage() }}"
                        alt="{{ $user->username }}'s profile"
                        class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="pt-20 md:pt-4 pb-8 px-8">
            <div class="md:ml-56 space-y-6">
                <!-- Username and Actions -->
                <div class="flex flex-col md:flex-row md:items-center gap-4 justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->username }}</h1>
                        <p class="text-gray-500 text-sm">Joined {{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @cannot('update', $user->profile)
                        <div x-data="{
                            isFollowing: {{ Auth::user()->following->contains($user->id) ? 'true' : 'false' }},
                            isLoading: false,
                            followerCount: {{ $followerCount }},
                            toggleFollow() {
                                this.isLoading = true;
                                const url = this.isFollowing
                                    ? '{{ route('unfollow', $user->id) }}'
                                    : '{{ route('follow', $user->id) }}';

                                fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({})
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    // Check if the response is JSON
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        return response.json();
                                    } else {
                                        // If not JSON, just return a success object
                                        return { success: true };
                                    }
                                })
                                .then(data => {
                                    this.isFollowing = !this.isFollowing;
                                    // Update follower count
                                    this.followerCount += this.isFollowing ? 1 : -1;
                                    // Update the follower count in the stats section
                                    document.querySelector('#followerCount').textContent = this.followerCount;
                                    this.isLoading = false;
                                })
                                .catch(error => {
                                    console.error('Error toggling follow:', error);
                                    this.isLoading = false;
                                });
                            }
                        }">
                            <button
                                @click="toggleFollow"
                                :disabled="isLoading"
                                :class="{
                                    'text-red-500 border-red-500 hover:bg-red-500': isFollowing,
                                    'text-blue-500 border-blue-500 hover:bg-blue-500': !isFollowing,
                                    'opacity-75 cursor-wait': isLoading
                                }"
                                class="px-6 py-2.5 text-sm font-medium hover:text-white border-2 rounded-xl transition-all duration-300 hover:shadow-md">
                                <span class="flex items-center gap-2">
                                    <svg x-show="isLoading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!isLoading && isFollowing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                    </svg>
                                    <svg x-show="!isLoading && !isFollowing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span x-text="isFollowing ? 'Unfollow' : 'Follow'"></span>
                                </span>
                            </button>
                        </div>
                        @endcannot

                        @can('update', $user->profile)
                        <a href="/p/create"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-xl transition-all duration-300 hover:shadow-md inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Post
                        </a>
                        <a href="/profile/{{ $user->id }}/edit"
                            class="px-6 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all duration-300 hover:shadow-md inline-flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex gap-8 py-4 border-y border-gray-100">
                    <div class="text-center">
                        <span class="block text-2xl font-bold text-gray-900">{{ $postCount }}</span>
                        <span class="text-sm text-gray-600">Posts</span>
                    </div>
                    <a href="{{ route('following.page', $user->id) }}"
                        class="text-center hover:text-blue-500 transition-colors group">
                        <span id="followerCount" class="block text-2xl font-bold text-gray-900 group-hover:text-blue-500">{{ $followerCount }}</span>
                        <span class="text-sm text-gray-600">Followers</span>
                    </a>
                    <a href="{{ route('following.page', $user->id) }}"
                        class="text-center hover:text-blue-500 transition-colors group">
                        <span class="block text-2xl font-bold text-gray-900 group-hover:text-blue-500">{{ $followingCount }}</span>
                        <span class="text-sm text-gray-600">Following</span>
                    </a>
                </div>

                <!-- Bio -->
                <div class="max-w-2xl">
                    <p class="text-gray-700 leading-relaxed">
                        {{ $user->profile->description ?: 'No bio yet.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="mt-12">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Posts</h2>

        @if($user->posts && $user->posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($user->posts as $post)
            <div class="relative group" x-data="{
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
                <a href="/p/{{ $post->id }}" class="block aspect-square overflow-hidden rounded-xl shadow-sm">
                    <img src="/storage/{{ $post->image }}"
                        alt="Post by {{ $user->username }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex items-center gap-6 text-white">
                            <button @click.prevent="toggleLike"
                                class="flex items-center gap-2 hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    :class="{ 'text-red-500 fill-current': isLiked }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="font-medium" x-text="likeCount"></span>
                            </button>
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="font-medium">{{ $post->comments ? $post->comments->count() : 0 }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No posts yet.</p>
        @endif
    </div>
</div>
@endsection