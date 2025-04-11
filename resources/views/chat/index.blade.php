@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Messages</h1>
        
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden transition-colors duration-200">
            <!-- Search bar -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <div class="relative">
                    <input 
                        type="text" 
                        id="search-users" 
                        placeholder="Search users..." 
                        class="w-full px-4 py-2 pl-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
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
                                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white dark:ring-gray-800 bg-green-400"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    @if($user->last_message)
                                        {{ Str::limit($user->last_message, 30) }}
                                    @else
                                        Start a conversation
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
                        <a href="{{ route('home') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Explore Users</a>
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
            const messagePreview = item.querySelector('.text-gray-500').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || messagePreview.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
