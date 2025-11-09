<div class="mt-8">
    <!-- User Info Card -->
    <div class="bg-white rounded-xl flex flex-col md:flex-row px-6 py-8 mb-6">
        <div class="flex-none mb-4 md:mb-0 md:mr-6">
            <img src="{{ $user->getAvatar() }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full object-cover">
        </div>
        <div class="flex-1">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-gray-600 mb-4">{{ $user->email }}</p>
                    @if ($user->isAdmin())
                        <span class="inline-block bg-red text-white text-xs px-3 py-1 rounded-full mb-4">Admin</span>
                    @endif
                </div>
                @auth
                    @if (auth()->id() !== $user->id)
                        <button
                            wire:click="toggleFollow"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 rounded-xl font-semibold text-sm transition duration-150 ease-in disabled:opacity-50 @if($isFollowing) bg-gray-200 text-gray-700 hover:bg-gray-300 @else bg-blue text-white hover:bg-blue-hover @endif"
                        >
                            <span wire:loading.remove wire:target="toggleFollow">
                                @if($isFollowing)
                                    Following
                                @else
                                    Follow
                                @endif
                            </span>
                            <span wire:loading wire:target="toggleFollow">...</span>
                        </button>
                    @endif
                @endauth
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap gap-6 mt-6">
                <div>
                    <div class="text-2xl font-bold text-blue">{{ $ideasCount }}</div>
                    <div class="text-sm text-gray-500">Ideas</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue">{{ $commentsCount }}</div>
                    <div class="text-sm text-gray-500">Comments</div>
                </div>
                <button wire:click="setActiveTab('followers')" class="cursor-pointer hover:opacity-80">
                    <div class="text-2xl font-bold text-blue">{{ $followersCount }}</div>
                    <div class="text-sm text-gray-500">Followers</div>
                </button>
                <button wire:click="setActiveTab('following')" class="cursor-pointer hover:opacity-80">
                    <div class="text-2xl font-bold text-blue">{{ $followingCount }}</div>
                    <div class="text-sm text-gray-500">Following</div>
                </button>
                <div>
                    <div class="text-2xl font-bold text-blue">{{ $user->created_at->format('M Y') }}</div>
                    <div class="text-sm text-gray-500">Member Since</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl mb-6">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            <button
                wire:click="setActiveTab('ideas')"
                class="px-6 py-4 font-semibold text-sm border-b-2 transition duration-150 ease-in whitespace-nowrap @if($activeTab === 'ideas') border-blue text-blue @else border-transparent text-gray-500 hover:text-gray-700 @endif"
            >
                Ideas ({{ $ideasCount }})
            </button>
            <button
                wire:click="setActiveTab('comments')"
                class="px-6 py-4 font-semibold text-sm border-b-2 transition duration-150 ease-in whitespace-nowrap @if($activeTab === 'comments') border-blue text-blue @else border-transparent text-gray-500 hover:text-gray-700 @endif"
            >
                Comments ({{ $commentsCount }})
            </button>
            <button
                wire:click="setActiveTab('followers')"
                class="px-6 py-4 font-semibold text-sm border-b-2 transition duration-150 ease-in whitespace-nowrap @if($activeTab === 'followers') border-blue text-blue @else border-transparent text-gray-500 hover:text-gray-700 @endif"
            >
                Followers ({{ $followersCount }})
            </button>
            <button
                wire:click="setActiveTab('following')"
                class="px-6 py-4 font-semibold text-sm border-b-2 transition duration-150 ease-in whitespace-nowrap @if($activeTab === 'following') border-blue text-blue @else border-transparent text-gray-500 hover:text-gray-700 @endif"
            >
                Following ({{ $followingCount }})
            </button>
        </div>
    </div>

    <!-- Content -->
    <div>
        @if ($activeTab === 'ideas')
            @if ($ideas && $ideas->count() > 0)
                <div class="space-y-4">
                    @foreach ($ideas as $idea)
                        <div class="idea-container hover:shadow-card transition duration-150 ease-in bg-white rounded-xl flex">
                            <div class="hidden md:block border-r border-gray-100 px-5 py-8">
                                <div class="text-center">
                                    <div class="font-semibold text-2xl text-gray-900">{{ $idea->votes_count }}</div>
                                    <div class="text-gray-500">Votes</div>
                                </div>
                            </div>
                            <div class="flex flex-col md:flex-row flex-1 px-2 py-6">
                                <div class="flex-none mx-2 md:mx-0">
                                    <a href="{{ route('user.show', $idea->user) }}">
                                        <img src="{{ $idea->user->getAvatar() }}" alt="avatar" class="w-14 h-14 rounded-xl">
                                    </a>
                                </div>
                                <div class="w-full flex flex-col justify-between mx-2 md:mx-4">
                                    <h4 class="text-xl font-semibold mt-2 md:mt-0">
                                        <a href="{{ route('idea.show', $idea) }}" class="idea-link hover:underline">{{ $idea->title }}</a>
                                    </h4>
                                    <div class="text-gray-600 mt-3 line-clamp-3">
                                        {{ $idea->description }}
                                    </div>

                                    <div class="flex flex-col md:flex-row md:items-center justify-between mt-6">
                                        <div class="flex items-center text-xs text-gray-400 font-semibold space-x-2">
                                            <div>{{ $idea->created_at->diffForHumans() }}</div>
                                            <div>&bull;</div>
                                            <div>{{ $idea->category->name }}</div>
                                            <div>&bull;</div>
                                            <div class="text-gray-900">{{ $idea->comments()->count() }} Comments</div>
                                        </div>
                                        <div class="flex items-center space-x-2 mt-4 md:mt-0">
                                            <div class="{{ 'status-'.Str::kebab($idea->status->name)}} text-xxs font-bold uppercase leading-none rounded-full text-center w-28 h-7 py-2 px-4">{{ $idea->status->name }}</div>
                                        </div>

                                        <div class="flex items-center md:hidden mt-4 md:mt-0">
                                            <div class="bg-gray-100 text-center rounded-xl h-10 px-4 py-2 pr-8">
                                                <div class="text-sm font-bold leading-none text-gray-900">{{ $idea->votes_count }}</div>
                                                <div class="text-xxs font-semibold leading-none text-gray-400">Votes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="my-8">
                    {{ $ideas->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl px-6 py-12 text-center">
                    <p class="text-gray-500">This user hasn't posted any ideas yet.</p>
                </div>
            @endif
        @elseif ($activeTab === 'comments')
            @if ($comments && $comments->count() > 0)
                <div class="space-y-4">
                    @foreach ($comments as $comment)
                        <div class="bg-white rounded-xl flex px-4 py-6">
                            <div class="flex-none mr-4">
                                <a href="{{ route('user.show', $comment->user) }}">
                                    <img src="{{ $comment->user->getAvatar() }}" alt="avatar" class="w-14 h-14 rounded-xl">
                                </a>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center text-xs text-gray-400 font-semibold space-x-2 mb-2">
                                    <a href="{{ route('user.show', $comment->user) }}" class="font-bold text-gray-900 hover:text-blue">
                                        {{ $comment->user->name }}
                                    </a>
                                    <div>&bull;</div>
                                    <div>{{ $comment->created_at->diffForHumans() }}</div>
                                    <div>&bull;</div>
                                    <a href="{{ route('idea.show', $comment->idea) }}" class="text-blue hover:underline">
                                        View Idea
                                    </a>
                                </div>
                                <div class="text-gray-600 mb-2">
                                    {{ $comment->body }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    On: <a href="{{ route('idea.show', $comment->idea) }}" class="text-blue hover:underline font-semibold">{{ $comment->idea->title }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="my-8">
                    {{ $comments->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl px-6 py-12 text-center">
                    <p class="text-gray-500">This user hasn't posted any comments yet.</p>
                </div>
            @endif
        @elseif ($activeTab === 'followers')
            @if ($followers && $followers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($followers as $follower)
                        <div class="bg-white rounded-xl p-4 hover:shadow-card transition duration-150 ease-in">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('user.show', $follower) }}">
                                    <img src="{{ $follower->getAvatar() }}" alt="{{ $follower->name }}" class="w-16 h-16 rounded-full object-cover">
                                </a>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('user.show', $follower) }}" class="block">
                                        <h3 class="font-semibold text-gray-900 truncate hover:text-blue">{{ $follower->name }}</h3>
                                    </a>
                                    <p class="text-xs text-gray-500 truncate">{{ $follower->email }}</p>
                                    <div class="flex space-x-3 mt-1 text-xs text-gray-400">
                                        <span>{{ $follower->ideas_count }} Ideas</span>
                                        <span>{{ $follower->comments_count }} Comments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="my-8">
                    {{ $followers->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl px-6 py-12 text-center">
                    <p class="text-gray-500">This user has no followers yet.</p>
                </div>
            @endif
        @elseif ($activeTab === 'following')
            @if ($following && $following->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($following as $followedUser)
                        <div class="bg-white rounded-xl p-4 hover:shadow-card transition duration-150 ease-in">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('user.show', $followedUser) }}">
                                    <img src="{{ $followedUser->getAvatar() }}" alt="{{ $followedUser->name }}" class="w-16 h-16 rounded-full object-cover">
                                </a>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('user.show', $followedUser) }}" class="block">
                                        <h3 class="font-semibold text-gray-900 truncate hover:text-blue">{{ $followedUser->name }}</h3>
                                    </a>
                                    <p class="text-xs text-gray-500 truncate">{{ $followedUser->email }}</p>
                                    <div class="flex space-x-3 mt-1 text-xs text-gray-400">
                                        <span>{{ $followedUser->ideas_count }} Ideas</span>
                                        <span>{{ $followedUser->comments_count }} Comments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="my-8">
                    {{ $following->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl px-6 py-12 text-center">
                    <p class="text-gray-500">This user is not following anyone yet.</p>
                </div>
            @endif
        @endif
    </div>
</div>

