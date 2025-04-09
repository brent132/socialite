<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Socialite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Left Side - Logo Section -->
        <div class="hidden lg:flex lg:w-1/2 bg-white items-center justify-center">
            <div class="text-center">
                <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-32 h-32 mx-auto mb-4">
                <h1 class="text-4xl font-bold">Socialite</h1>
                <p class="mt-4 text-gray-600">Connect with friends and share your moments</p>
            </div>
        </div>

        <!-- Right Side - Form Section -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-slate-800 shadow-md rounded-lg px-8 py-6">
                    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800 dark:text-gray-100">{{ __('Reset Password') }}</h2>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ __('Email Address') }}</label>
                            <input id="email" type="email" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-slate-700 dark:border-slate-600 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 @error('email') border-red-500 @enderror" 
                                name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ __('Password') }}</label>
                            <input id="password" type="password" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-slate-700 dark:border-slate-600 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 @error('password') border-red-500 @enderror" 
                                name="password" required autocomplete="new-password">
                            @error('password')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password-confirm" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-slate-700 dark:border-slate-600 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" 
                                name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                                {{ __('Reset Password') }}
                            </button>
                            
                            <div class="text-sm">
                                <a href="{{ route('login') }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                    Back to Login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
