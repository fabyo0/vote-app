<div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 border border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ $poll->question }}</h3>

    @if($showResults || !$poll->canVote())
        <!-- Show Results -->
        <div class="space-y-3">
            @foreach($voteCounts as $index => $data)
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $data['option'] }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $data['votes'] }} votes ({{ $data['percentage'] }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div 
                            class="bg-blue dark:bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                            style="width: {{ $data['percentage'] }}%"
                            role="progressbar"
                            aria-valuenow="{{ $data['percentage'] }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                    </div>
                    @if($selectedOption === $index && auth()->check())
                        <span class="text-xs text-blue dark:text-blue-400 mt-1 block">Your vote</span>
                    @endif
                </div>
            @endforeach
        </div>
        
        @if($poll->ends_at)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                Poll ends: {{ $poll->ends_at->format('M d, Y H:i') }}
            </p>
        @endif
    @else
        <!-- Voting Form -->
        <form wire:submit.prevent="vote" class="space-y-3">
            @foreach($poll->options as $index => $option)
                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <input 
                        type="radio" 
                        wire:model="selectedOption" 
                        value="{{ $index }}"
                        class="mr-3 text-blue focus:ring-blue"
                        aria-label="Vote for {{ $option }}"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option }}</span>
                </label>
            @endforeach

            <button 
                type="submit"
                class="w-full mt-4 bg-blue dark:bg-blue-600 text-white font-semibold rounded-xl py-2 px-4 hover:bg-blue-hover dark:hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Vote</span>
                <span wire:loading>Voting...</span>
            </button>
        </form>

        @if($poll->ends_at)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                Poll ends: {{ $poll->ends_at->format('M d, Y H:i') }}
            </p>
        @endif
    @endif

    @if(!$showResults && $poll->canVote() && auth()->check())
        <button 
            wire:click="showResults"
            class="mt-3 text-sm text-blue dark:text-blue-400 hover:underline focus:outline-none focus:ring-2 focus:ring-blue rounded"
        >
            Show results without voting
        </button>
    @endif
</div>
