<div class="bg-white rounded-xl flex flex-col md:flex-row mt-8">
    <div class="flex flex-col md:flex-row flex-1 px-4 py-6">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-6">Admin Settings</h2>

            <!-- Notification Channels Section -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Notification System</h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Notification Channels</label>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                wire:model="notificationChannels"
                                value="database"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <span class="ml-3">
                                <span class="font-medium text-gray-900">Database Only</span>
                                <span class="block text-sm text-gray-500">Store notifications in database (polling required)</span>
                            </span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                wire:model="notificationChannels"
                                value="broadcast"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <span class="ml-3">
                                <span class="font-medium text-gray-900">Pusher Only</span>
                                <span class="block text-sm text-gray-500">Real-time notifications via Pusher (no database storage)</span>
                            </span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input
                                type="radio"
                                wire:model="notificationChannels"
                                value="database,broadcast"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                            >
                            <span class="ml-3">
                                <span class="font-medium text-gray-900">Both (Recommended)</span>
                                <span class="block text-sm text-gray-500">Database + real-time Pusher notifications</span>
                            </span>
                        </label>
                    </div>

                    @error('notificationChannels')
                        <span class="text-red-600 text-sm mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Current Selection:
                                @if($notificationChannels === 'database')
                                    Database Only
                                @elseif($notificationChannels === 'broadcast')
                                    Pusher Only
                                @else
                                    Both (Database + Pusher)
                                @endif
                            </p>
                            <p>This setting affects all users in the application.</p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <button
                    wire:click="saveSettings"
                    wire:loading.attr="disabled"
                    class="w-full md:w-auto px-6 py-2 bg-blue text-white font-semibold rounded-xl hover:bg-blue-hover transition duration-150 ease-in disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveSettings">Save Settings</span>
                    <span wire:loading wire:target="saveSettings">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>