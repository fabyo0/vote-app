<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Voting App</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <livewire:styles/>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="font-sans bg-gray-background text-gray-900 text-sm">
<header class="flex flex-col md:flex-row items-center justify-between px-8 py-4">
    <x-application-logo style="width: 70px;" />
    <div class="flex items-center mt-2 md:mt-0">
        @if (Route::has('login'))
            @auth
                <div class="flex items-center space-x-4">
                    <!-- Notification -->
                    <livewire:comment-notifications/>

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 focus:outline-none">
                            <img src="{{ auth()->user()->getAvatar() }}" alt="avatar" class="w-10 h-10 rounded-full">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                             style="display: none;">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 underline">Register</a>
                    @endif
                    <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp" alt="avatar"
                         class="w-10 h-10 rounded-full">
                </div>
            @endauth
        @endif
    </div>
</header>

<main class="container mx-auto max-w-custom flex flex-col md:flex-row">
    <div class="w-70 mx-auto md:mx-0 md:mr-5">
        <div
            class="bg-white md:sticky md:top-8 border-2 border-blue rounded-xl mt-16"
            style="
                          border-image-source: linear-gradient(to bottom, rgba(50, 138, 241, 0.22), rgba(99, 123, 255, 0));
                            border-image-slice: 1;
                            background-image: linear-gradient(to bottom, #ffffff, #ffffff), linear-gradient(to bottom, rgba(50, 138, 241, 0.22), rgba(99, 123, 255, 0));
                            background-origin: border-box;
                            background-clip: content-box, border-box;
                    "
        >
            <div class="text-center px-6 py-2 pt-6">
                <h3 class="font-semibold text-base">Add an idea</h3>
                <p class="text-xs mt-4">
                    @auth
                        Let us know what you would like and we'll take a look over!
                    @else
                        Please login to create an idea.
                    @endauth
                </p>
            </div>

            @auth
                <livewire:create-idea/>
            @else
                <div class="my-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-block justify-center w-1/2 h-11 text-xs bg-blue text-white font-semibold rounded-xl border border-blue hover:bg-blue-hover transition duration-150 ease-in px-6 py-3"
                    >
                        Login
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-block justify-center w-1/2 h-11 text-xs bg-gray-200 font-semibold rounded-xl border border-gray-200 hover:border-gray-400 transition duration-150 ease-in px-6 py-3 mt-4"
                    >
                        Sign Up
                    </a>
                </div>
            @endauth

        </div>
    </div>
    <div class="w-full px-2 md:px-0 md:w-175">
        <livewire:status-filter/>

        <div class="mt-8">
            {{ $slot }}
        </div>
    </div>
</main>

@if (session('success_message'))
    <x-notification-success
        :redirect="true"
        message-to-display="{{ (session('success_message')) }}"
    />
@endif


@if (session('error_message'))
    <x-notification-success
        type="error"
        :redirect="true"
        message-to-display="{{ (session('error_message')) }}"
    />
@endif

<livewire:scripts/>
</body>
</html>
