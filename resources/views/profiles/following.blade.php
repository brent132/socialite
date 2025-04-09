@extends('layouts.app')

@section('content')
<div class="container w-[600px] mx-auto py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Following</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Manage your connections</p>
    </div>

    <!-- People You Follow Section -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="border-b border-gray-100 dark:border-slate-700 p-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">People You Follow</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Users you're currently following</p>
        </div>
        
        <div class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($following as $user)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile->profileImage() }}" 
                             alt="{{ $user->username }}'s profile" 
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-100 dark:border-slate-600">
                        <div>
                            <a href="/profile/{{ $user->id }}" 
                               class="font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-500 transition-colors">
                                {{ $user->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Following</p>
                        </div>
                    </div>
                    @if(Auth::user()->id !== $user->id)
                    <form action="{{ route('unfollow', $user->id) }}" method="POST">
                        @csrf                   
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-red-500 hover:text-white border border-red-500 hover:bg-red-500 rounded-full transition-colors duration-300">
                            Unfollow
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="text-gray-400 dark:text-gray-500 mb-2">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">You're not following anyone yet.</p>
                </div>
                <a href="/explore" class="text-blue-500 hover:text-blue-600 font-medium">Discover people to follow →</a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Suggested Users Section -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 dark:border-slate-700 p-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Suggested for You</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">People you might want to follow</p>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($notFollowing as $user)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile->profileImage() }}" 
                             alt="{{ $user->username }}'s profile" 
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-100 dark:border-slate-600">
                        <div>
                            <a href="/profile/{{ $user->id }}" 
                               class="font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-500 transition-colors">
                                {{ $user->username }}
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Suggested for you</p>
                        </div>
                    </div>
                    @if(Auth::user()->id !== $user->id)
                    <form action="{{ route('follow', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-blue-500 hover:text-white border border-blue-500 hover:bg-blue-500 rounded-full transition-colors duration-300">
                            Follow
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <div class="text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No suggestions available at the moment.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
