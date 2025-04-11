<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Socialite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-button {
            background: linear-gradient(to right, #4776E6, #8E54E9);
            transition: all 0.3s ease;
        }

        .gradient-button:hover {
            background: linear-gradient(to right, #3A5FCD, #7B3FE4);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .input-field:focus {
            border-color: #8E54E9;
            box-shadow: 0 0 0 3px rgba(142, 84, 233, 0.2);
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: background-color 0.2s ease-in-out;
        }

        .dark .login-card {
            background: #1f2937;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <div class="min-h-screen flex">
        <!-- Left Side - Logo Section with enhanced styling -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-indigo-900 items-center justify-center transition-colors duration-200">
            <div class="text-center p-12">
                <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-40 h-40 mx-auto mb-6 hover-transform">
                <h1 class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-600">Socialite</h1>
                <p class="mt-6 text-gray-700 dark:text-gray-300 text-lg max-w-md mx-auto">Connect with friends and share your moments in a beautiful social experience</p>
            </div>
        </div>

        <!-- Right Side - Form Section with improved layout -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-200">
            <div class="w-full max-w-md">
                <!-- Mobile logo (visible only on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-24 h-24 mx-auto mb-2">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-600">Socialite</h1>
                </div>

                <div class="login-card px-8 py-8 relative">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="absolute top-4 right-4 text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <h2 class="text-2xl font-bold mb-8 text-center text-gray-800 dark:text-gray-100">Welcome Back</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">{{ __('Email Address') }}</label>
                            <input id="email" type="email"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 dark:text-gray-200 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none @error('email') border-red-500 @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">{{ __('Password') }}</label>
                            <input id="password" type="password"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 dark:text-gray-200 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none @error('password') border-red-500 @enderror"
                                name="password" required autocomplete="current-password">
                            @error('password')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center">
                                <input class="w-4 h-4 mr-2 accent-purple-600" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="text-sm text-gray-600 dark:text-gray-400" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                            @endif
                        </div>

                        <div class="mb-8">
                            <button type="submit" class="gradient-button w-full text-white font-bold py-3 px-4 rounded-lg focus:outline-none">
                                {{ __('Login') }}
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-gray-600 dark:text-gray-400 mb-2">Don't have an account?</p>
                            <a href="{{ route('register') }}" class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-300">
                                Create an Account
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>