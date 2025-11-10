<div class="bg-white rounded-xl flex flex-col md:flex-row mt-8">
    <div class="flex flex-col md:flex-row flex-1 px-4 py-6">
        <div class="w-full">
            <h2 class="text-2xl font-bold mb-6">Profile Settings</h2>

            <!-- Profile Information Section -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Profile Information</h3>
                
                <!-- Avatar Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if ($temporaryAvatar)
                                <img src="{{ $temporaryAvatar['url'] }}" alt="Avatar preview" class="w-24 h-24 rounded-full object-cover">
                            @else
                                <img src="{{ $user->getAvatar() }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover">
                            @endif
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-sm font-semibold transition duration-150 ease-in">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="avatar">Upload Avatar</span>
                                <span wire:loading wire:target="avatar">Uploading...</span>
                                <input type="file" wire:model="avatar" class="hidden" accept="image/*">
                            </label>
                            @if ($temporaryAvatar || $user->getFirstMediaUrl('avatar'))
                                <button wire:click="removeAvatar" class="text-sm text-red-600 hover:text-red-800">
                                    Remove Avatar
                                </button>
                            @endif
                            @error('avatar')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                            <div wire:loading wire:target="avatar" class="text-sm text-blue">
                                Processing image...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Name Field -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name" 
                        class="w-full rounded-xl border-none bg-gray-100 px-4 py-2 focus:ring-2 focus:ring-blue focus:outline-none @error('name') border-red-500 @enderror"
                        placeholder="Your name"
                    >
                    @error('name')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email"
                        wire:model="email" 
                        class="w-full rounded-xl border-none bg-gray-100 px-4 py-2 focus:ring-2 focus:ring-blue focus:outline-none @error('email') border-red-500 @enderror"
                        placeholder="your@email.com"
                    >
                    @error('email')
                        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Update Profile Button -->
                <button 
                    wire:click="updateProfile"
                    wire:loading.attr="disabled"
                    class="w-full md:w-auto px-6 py-2 bg-blue text-white font-semibold rounded-xl hover:bg-blue-hover transition duration-150 ease-in disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="updateProfile">Update Profile</span>
                    <span wire:loading wire:target="updateProfile">Updating...</span>
                </button>
            </div>

            <!-- Password Section -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold">Password</h3>
                    <button 
                        wire:click="togglePasswordForm"
                        class="text-blue hover:text-blue-hover font-semibold"
                    >
                        {{ $showPasswordForm ? 'Cancel' : 'Change Password' }}
                    </button>
                </div>

                @if ($showPasswordForm)
                    <div class="space-y-4">
                        <!-- Current Password -->
                        <div>
                            <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input 
                                type="password" 
                                id="currentPassword"
                                wire:model="currentPassword" 
                                class="w-full rounded-xl border-none bg-gray-100 px-4 py-2 focus:ring-2 focus:ring-blue focus:outline-none @error('currentPassword') border-red-500 @enderror"
                                placeholder="Enter current password"
                            >
                            @error('currentPassword')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input 
                                type="password" 
                                id="newPassword"
                                wire:model="newPassword" 
                                class="w-full rounded-xl border-none bg-gray-100 px-4 py-2 focus:ring-2 focus:ring-blue focus:outline-none @error('newPassword') border-red-500 @enderror"
                                placeholder="Enter new password"
                            >
                            @error('newPassword')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="newPasswordConfirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="newPasswordConfirmation"
                                wire:model="newPasswordConfirmation" 
                                class="w-full rounded-xl border-none bg-gray-100 px-4 py-2 focus:ring-2 focus:ring-blue focus:outline-none"
                                placeholder="Confirm new password"
                            >
                        </div>

                        <!-- Update Password Button -->
                        <button 
                            wire:click="updatePassword"
                            wire:loading.attr="disabled"
                            class="w-full md:w-auto px-6 py-2 bg-blue text-white font-semibold rounded-xl hover:bg-blue-hover transition duration-150 ease-in disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                            <span wire:loading wire:target="updatePassword">Updating...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

