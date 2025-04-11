@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Following Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700 p-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Following</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">People you follow</p>
            </div>
        
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($following as $user)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ $user->profile->profileImage() }}" 
                                 alt="{{ $user->username }}'s profile" 
                                 class="w-12 h-12 rounded-full object-cover border-2 border-gray-100 dark:border-gray-600">
                            <div>
                                <a href="/profile/{{ $user->id }}" 
                                   class="font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    {{ $user->username }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Following</p>
                            </div>
                        </div>
                        @if(Auth::user()->id !== $user->id)
                        <form action="{{ route('unfollow', $user->id) }}" method="POST">
                            @csrf                   
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-red-500 dark:text-red-400 hover:text-white dark:hover:text-white border border-red-500 dark:border-red-400 hover:bg-red-500 dark:hover:bg-red-500 rounded-full transition-colors">
                                Unfollow
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">You're not following anyone yet.</p>
                    <a href="/explore" class="text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 font-medium">Discover people to follow →</a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Suggested Users Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mt-6">
            <div class="border-b border-gray-100 dark:border-gray-700 p-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Suggested for You</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">People you might want to follow</p>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($notFollowing as $user)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ $user->profile->profileImage() }}" 
                                 alt="{{ $user->username }}'s profile" 
                                 class="w-12 h-12 rounded-full object-cover border-2 border-gray-100 dark:border-gray-600">
                            <div>
                                <a href="/profile/{{ $user->id }}" 
                                   class="font-semibold text-gray-800 dark:text-gray-100 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                                    {{ $user->username }}
                                </a>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Suggested for you</p>
                            </div>
                        </div>
                        @if(Auth::user()->id !== $user->id)
                        <form action="{{ route('follow', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 text-sm font-medium text-blue-500 dark:text-blue-400 hover:text-white dark:hover:text-white border border-blue-500 dark:border-blue-400 hover:bg-blue-500 dark:hover:bg-blue-500 rounded-full transition-colors">
                                Follow
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No suggestions available at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection


