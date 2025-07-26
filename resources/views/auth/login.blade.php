<x-guest-layout>
    <x-auth-card class="rounded-md shadow-md p-8 bg-white">
        <x-slot name="logo">
            <a href="/" class="flex justify-center mb-6">
                <x-application-logo class="w-20 h-20 text-gray-800" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            @honeypot

            <!-- Email -->
            <div>
                <x-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-800" />
                <x-input id="email" type="email" name="email"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    :value="old('email')" required autofocus />
            </div>

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-800" />
                <x-input id="password" type="password" name="password"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    required autocomplete="current-password" />
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-700">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-500">
                    <span class="ml-2">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:underline">
                        Forgot your password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div>
                <button type="submit"
                    class="w-full justify-center inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white tracking-wide hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 transition ease-in-out duration-150">
                    Log in
                </button>
            </div>

            <x-social-links/>
        </form>
    </x-auth-card>
</x-guest-layout>
