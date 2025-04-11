@extends('layouts.app')

@section('content')
<div class="container max-w-[768px] mx-auto px-4">
    <div class="bg-white dark:bg-gray-800 rounded-md shadow-md p-4 transition-colors duration-200">
        <div x-data="{ searchQuery: '', searchResults: [], isSearching: false }">
            <!-- Search Input -->
            <div class="relative">
                <input
                    type="text"
                    placeholder="Search profiles..."
                    x-model="searchQuery"
                    @input.debounce.300ms="
                        if (searchQuery.length > 0) {
                            isSearching = true;
                            fetch(`/search?query=${searchQuery}`)
                                .then(res => res.json())
                                .then(data => {
                                    searchResults = data;
                                    isSearching = false;
                                });
                        } else {
                            searchResults = [];
                        }
                    "
                    class="w-full bg-gray-100 dark:bg-gray-700 dark:text-gray-200 px-4 py-3 rounded-lg text-sm focus:outline-none transition-colors duration-200">

                <!-- Loading indicator -->
                <div
                    x-show="isSearching"
                    class="absolute right-3 top-3">
                    <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Search Results -->
            <div
                x-show="searchResults.length > 0"
                class="mt-4">
                <template x-for="user in searchResults" :key="user.id">
                    <a
                        :href="`/profile/${user.id}`"
                        class="flex items-center space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                        <img :src="user.profile.image ? `/storage/${user.profile.image}` : '/storage/profile/default-avatar.png'"
                            class="w-12 h-12 rounded-full object-cover"
                            :alt="user.username">
                        <div>
                            <div x-text="user.username" class="font-medium dark:text-gray-200"></div>
                            <div x-text="user.name" class="text-sm text-gray-500 dark:text-gray-400"></div>
                        </div>
                    </a>
                </template>
            </div>

            <!-- No Results Message -->
            <div
                x-show="searchQuery.length > 0 && searchResults.length === 0 && !isSearching"
                class="mt-4 text-center text-gray-500 dark:text-gray-400">
                No results found
            </div>
        </div>
    </div>
</div>
@endsection