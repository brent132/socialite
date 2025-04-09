<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Socialite</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
        }

        .hover-scale {
            transition: transform 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.03);
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans relative min-h-screen text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div id="app" class="flex flex-col min-h-screen">
        @auth
        <nav class="sticky top-0 z-50 glass-effect border-b border-gray-200 dark:border-gray-700 bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between items-center">
                    <!-- Logo -->
                    <a href="{{ url('/home') }}" class="flex items-center space-x-2">
                        <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-[30px] h-[30px]">
                        <div class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-600">Socialite</div>
                    </a>

                    <!-- Right Side Navigation -->
                    <div class="flex items-center space-x-6">
                        <!-- Home Icon -->
                        <a href="{{ url('/home') }}" class="text-gray-600 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </a>

                        <!-- Create Post Icon -->
                        <a href="/p/create" class="text-gray-600 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </a>

                        <!-- Messages Icon -->
                        <a href="{{ route('chat.list') }}" class="text-gray-600 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </a>

                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="text-gray-600 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:text-indigo-400 focus:outline-none">
                            <!-- Sun icon for dark mode (show when dark mode is active) -->
                            <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Moon icon for light mode (show when dark mode is not active) -->
                            <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Search Icon for Mobile -->
                        <a href="{{ route('search.page') }}" class="md:hidden text-gray-600 hover:text-indigo-600 transition-colors dark:text-gray-300 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>

                        <!-- Desktop Search -->
                        <div class="hidden md:block relative" x-data="{ searchQuery: '', searchResults: [], isSearching: false }">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
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
                                    @click.away="searchResults = []"
                                    class="bg-gray-100 pl-10 pr-4 py-2 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-64 transition-all">
                            </div>

                            <!-- Search Results Dropdown -->
                            <div
                                x-show="searchResults.length > 0"
                                class="absolute mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg py-2 z-50 border border-gray-100 dark:border-gray-700">
                                <template x-for="user in searchResults" :key="user.id">
                                    <a
                                        :href="`/profile/${user.id}`"
                                        class="flex px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 items-center space-x-3">
                                        <img :src="user.profile.image ? `/storage/${user.profile.image}` : '/storage/profile/default-avatar.png'"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600"
                                            :alt="user.username">
                                        <div>
                                            <div x-text="user.username" class="font-medium"></div>
                                            <div x-text="user.name" class="text-xs text-gray-500 dark:text-gray-400"></div>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <!-- Loading indicator -->
                            <div
                                x-show="isSearching"
                                class="absolute right-3 top-2">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none" id="navbarDropdown">
                                <div class="relative">
                                    <img src="{{ Auth::user()->profile->profileImage() }}"
                                        alt="Profile"
                                        class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
                                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 rounded-full border border-white"></div>
                                </div>
                                <span class="font-medium text-sm hidden md:block">{{ Auth::user()->username }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg py-2 z-50 border border-gray-100 dark:border-gray-700"
                                id="dropdownMenu">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()->username }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="/profile/{{ Auth::user()->id }}"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Profile
                                </a>
                                <a href="/p/create"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create Post
                                </a>
                                <a href="{{ route('chat.list') }}"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    Messages
                                </a>
                                <hr class="my-1 border-gray-100 dark:border-gray-700">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        @endauth

        <main class="py-8 flex-grow">
            <div class="container mx-auto px-4">
                @yield('content')
            </div>
        </main>

        <footer class="py-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-2 mb-4 md:mb-0">
                        <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-[20px] h-[20px]">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-300">© 2023 Socialite. All rights reserved.</div>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm">About</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm">Privacy</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm">Terms</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 text-sm">Help</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>