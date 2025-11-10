<x-guest-layout>
    <x-auth-card class="rounded-md shadow-md p-8 bg-white">
        <x-slot name="logo">
            <a href="/" class="flex justify-center mb-6">
                <x-application-logo class="w-20 h-20 text-gray-800" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            @honeypot

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" class="text-sm font-medium text-gray-800" />
                <x-input id="name" type="text" name="name"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    :value="old('name')" required autofocus />
            </div>

            <!-- Username -->
            <div>
                <x-label for="username" :value="__('Username')" class="text-sm font-medium text-gray-800" />
                <x-input id="username" type="text" name="username"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    :value="old('username')" required />
                <p class="mt-1 text-xs text-gray-500">Only letters, numbers, and underscores. No spaces.</p>
            </div>

            <!-- Email -->
            <div>
                <x-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-800" />
                <x-input id="email" type="email" name="email"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    :value="old('email')" required />
            </div>

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-800" />
                <x-input id="password" type="password" name="password"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-800" />
                <x-input id="password_confirmation" type="password" name="password_confirmation"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-200 text-sm"
                    required />
            </div>

            <!-- Already registered? -->
            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:underline">
                    {{ __('Already registered?') }}
                </a>

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white tracking-wide hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 transition ease-in-out duration-150">
                    {{ __('Register') }}
                </button>
            </div>
        </form>

        <!-- Social Links -->
         <x-social-links/>
    </x-auth-card>
</x-guest-layout>
