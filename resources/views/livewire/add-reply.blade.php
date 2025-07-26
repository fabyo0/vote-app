<form wire:submit.prevent="addReply" class="space-y-4">
    <div>
        <textarea
            wire:model.defer="reply"
            name="reply"
            id="reply-{{ $parentComment->id }}"
            cols="30"
            rows="3"
            class="w-full text-sm bg-gray-100 rounded-xl placeholder-gray-900 border-none px-4 py-2"
            placeholder="Write your reply..."
            required
        ></textarea>

        @error('reply')
            <p class="text-red text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center space-x-3">
        <button
            type="submit"
            class="flex items-center justify-center h-9 px-4 text-xs bg-blue text-white font-semibold rounded-lg border border-blue hover:bg-blue-hover transition duration-150 ease-in"
        >
            Post Reply
        </button>
        <button
            type="button"
            wire:click="$parent.toggleReplyForm"
            class="flex items-center justify-center h-9 px-4 text-xs bg-gray-200 font-semibold rounded-lg border border-gray-200 hover:border-gray-400 transition duration-150 ease-in"
        >
            Cancel
        </button>
    </div>
</form>
