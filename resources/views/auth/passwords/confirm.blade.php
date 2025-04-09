@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-slate-800 shadow-md rounded-lg px-8 py-6">
                    <div class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">{{ __('Confirm Password') }}</div>

                    <div class="mb-6 text-gray-700 dark:text-gray-300">
                        {{ __('Please confirm your password before continuing.') }}
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">{{ __('Password') }}</label>

                            <input id="password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-slate-700 dark:border-slate-600 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-indigo-400 @error('password') border-red-500 @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                            <p class="text-red-500 text-xs italic mt-2">
                                <strong>{{ $message }}</strong>
                            </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                                {{ __('Confirm Password') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="inline-block align-baseline font-bold text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
