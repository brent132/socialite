<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

        .register-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Left Side - Logo Section with enhanced styling -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-50 to-indigo-100 items-center justify-center">
            <div class="text-center p-12">
                <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-40 h-40 mx-auto mb-6 hover-transform">
                <h1 class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-600">Socialite</h1>
                <p class="mt-6 text-gray-700 text-lg max-w-md mx-auto">Join our community and start sharing your moments with friends and family</p>
            </div>
        </div>

        <!-- Right Side - Form Section with improved layout -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="w-full max-w-md">
                <!-- Mobile logo (visible only on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <img src="/SVG/Socialite logo.svg" alt="Socialite Logo" class="w-24 h-24 mx-auto mb-2">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-600">Socialite</h1>
                </div>

                <div class="register-card px-8 py-8">
                    <h2 class="text-2xl font-bold mb-8 text-center text-gray-800">Create Your Account</h2>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-5">
                            <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">{{ __('Full Name') }}</label>
                            <input id="name" type="text"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 leading-tight focus:outline-none @error('name') border-red-500 @enderror"
                                name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">{{ __('Email Address') }}</label>
                            <input id="email" type="email"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 leading-tight focus:outline-none @error('email') border-red-500 @enderror"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">{{ __('Username') }}</label>
                            <input id="username" type="text"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 leading-tight focus:outline-none @error('username') border-red-500 @enderror"
                                name="username" value="{{ old('username') }}" required>
                            @error('username')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">{{ __('Password') }}</label>
                            <input id="password" type="password"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 leading-tight focus:outline-none @error('password') border-red-500 @enderror"
                                name="password" required>
                            @error('password')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-7">
                            <label for="password-confirm" class="block text-gray-700 text-sm font-semibold mb-2">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password"
                                class="input-field w-full py-3 px-4 rounded-lg text-gray-700 leading-tight focus:outline-none"
                                name="password_confirmation" required>
                        </div>

                        <div class="mb-7">
                            <button type="submit" class="gradient-button w-full text-white font-bold py-3 px-4 rounded-lg focus:outline-none">
                                {{ __('Create Account') }}
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-gray-600 mb-2">Already have an account?</p>
                            <a href="{{ route('login') }}" class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-300">
                                Login to Your Account
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>