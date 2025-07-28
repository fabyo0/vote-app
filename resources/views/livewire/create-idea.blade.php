<form wire:submit.prevent="createIdea" method="POST" class="space-y-4 px-4 py-6">
    <div>
        <input wire:model="title" type="text"
               class="w-full text-sm bg-gray-100 border-none rounded-xl placeholder-gray-900 px-4 py-2"
               placeholder="Your Idea">
        @error('title')
        <p class="text-red text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <select wire:model="category" id="category_add"
                class="w-full bg-gray-100 text-sm rounded-xl border-none px-4 py-2">
            <option value="{{ null }}" selected>Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    @error('category')
    <p class="text-red text-xs mt-1">{{ $message }}</p>
    @enderror

    <div>
        <textarea wire:model.defer="description" id="idea" cols="30" rows="4"
                  class="w-full bg-gray-100 rounded-xl border-none placeholder-gray-900 text-sm px-4 py-2"
                  placeholder="Describe your idea"></textarea>
    </div>
    @error('description')
    <p class="text-red text-xs mt-1">{{ $message }}</p>
    @enderror

    @if(!empty($temporaryImages))
        <div class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700">{{ __('Uploaded Images') }}</h4>
            <div class="grid grid-cols-2 gap-3">
                @foreach($temporaryImages as $index => $tempImage)
                    <div class="relative bg-gray-50 rounded-lg p-2 border border-gray-200">
                        <img src="{{ $tempImage['url'] }}"
                             alt="Preview"
                             class="w-full h-20 object-cover rounded-md">

                        <div class="mt-1 flex items-center justify-between">
                            <div class="text-xs text-gray-600 truncate mr-2">
                                <div class="font-medium">{{ Str::limit($tempImage['name'], 15) }}</div>
                                <div class="text-gray-500">{{ $tempImage['size'] }}</div>
                            </div>

                            <button type="button"
                                    wire:click="removeImage({{ $index }})"
                                    class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    @error('images.*')
    <p class="text-red text-xs mt-1">{{ $message }}</p>
    @enderror

    <div class="flex items-center justify-between space-x-3">

        <input type="file"
               wire:model="images"
               id="image-upload"
               multiple
               accept="image/*"
               class="hidden">

        <!-- Attach Button -->
        <label for="image-upload"
               class="flex items-center justify-center w-1/2 h-11 text-xs bg-gray-200 font-semibold rounded-xl border border-gray-200 hover:border-gray-400 transition duration-150 ease-in px-6 py-3 cursor-pointer">
            <svg class="text-gray-600 w-4 transform -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            <span class="ml-1">Attach</span>
            @if(!empty($temporaryImages))
                <span class="ml-1 bg-blue text-white rounded-full px-2 py-0.5 text-xs">{{ count($temporaryImages) }}</span>
            @endif
        </label>

        <!-- Submit Button -->
        <button type="submit"
                class="flex items-center justify-center w-1/2 h-11 text-xs bg-blue text-white font-semibold rounded-xl border border-blue hover:bg-blue-hover transition duration-150 ease-in px-6 py-3"
                wire:loading.attr="disabled"
                wire:target="createIdea">
            <span wire:loading.remove wire:target="createIdea" class="ml-1">Submit</span>
            <span wire:loading wire:target="createIdea" class="ml-1">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
            </span>
        </button>
    </div>

    <div>
        <x-flash-messasge/>
    </div>
</form>
