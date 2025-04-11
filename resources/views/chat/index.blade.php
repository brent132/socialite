@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Messages</h1>
        
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transition-colors duration-200">
            <!-- Search bar -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="search-users" class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 transition-colors duration-200" placeholder="Search people...">
                </div>
            </div>

            <!-- User list -->
            <div class="divide-y divide-gray-100 dark:divide-gray-700" id="user-list">
                @if($following->count() > 0)
                    @foreach($following as $user)
                    <a href="{{ route('chat.show', $user->id) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out">
                        <div class="p-4 flex items-center space-x-4">
                            <div class="relative">
                                <img src="{{ $user->profile->profileImage() }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                                @if($user->last_message)
                                    <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white dark:ring-gray-800 {{ $user->is_sender ? 'bg-indigo-400' : 'bg-green-400' }}"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate flex items-center space-x-1">
                                    @if($user->last_message)
                                        @if($user->is_sender)
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                You:
                                            </span>
                                        @else
                                            <span class="inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $user->name }}:
                                            </span>
                                        @endif
                                        {{ Str::limit($user->last_message, 30) }}
                                    @else
                                        <span class="text-gray-400 italic">Start a conversation</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                @if($user->last_message_time)
                                    <span>{{ $user->last_message_time->diffForHumans(null, true) }}</span>
                                @else
                                    <span>New</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                @else
                    <div class="p-8 text-center">
                        <p class="text-gray-500 dark:text-gray-400">You're not following anyone yet.</p>
                        <a href="/search-page" class="mt-2 inline-block text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors duration-200">Find people to follow</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Simple client-side search functionality
    document.getElementById('search-users').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const userItems = document.querySelectorAll('#user-list > a');

        userItems.forEach(item => {
            const userName = item.querySelector('.text-gray-900').textContent.toLowerCase();
            const messageText = item.querySelector('.text-gray-500').textContent.toLowerCase();

            if (userName.includes(searchTerm) || messageText.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection


