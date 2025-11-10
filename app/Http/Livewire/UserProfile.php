<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class UserProfile extends Component
{
    use WithFileUploads;

    public $user;
    public $name;
    public $email;
    public $avatar;
    public $temporaryAvatar;
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;
    public $showPasswordForm = false;

    protected array $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'avatar' => 'nullable|image|max:2048', // Max 2MB
        'currentPassword' => 'required_with:newPassword|string',
        'newPassword' => 'nullable|string|min:8|confirmed',
    ];

    protected array $messages = [
        'avatar.image' => 'You can only upload image files.',
        'avatar.max' => 'The avatar must be no larger than 2MB.',
        'newPassword.confirmed' => 'The new password confirmation does not match.',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function updatedAvatar(): void
    {
        $this->validateOnly('avatar');

        if ($this->avatar) {
            $this->temporaryAvatar = [
                'name' => $this->avatar->getClientOriginalName(),
                'url' => $this->avatar->temporaryUrl(),
            ];
        }
    }

    public function removeAvatar(): void
    {
        $this->avatar = null;
        $this->temporaryAvatar = null;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Update name and email
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            $this->user->clearMediaCollection('avatar');

            // Add new avatar
            $this->user->addMediaFromStream($this->avatar->readStream())
                ->usingName($this->avatar->getClientOriginalName())
                ->usingFileName($this->avatar->getClientOriginalName())
                ->toMediaCollection('avatar');
        }

        $this->reset(['avatar', 'temporaryAvatar']);
        $this->user->refresh();

        $this->emit('profileWasUpdated', 'Profile updated successfully!');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (! Hash::check($this->currentPassword, $this->user->password)) {
            $this->addError('currentPassword', 'The current password is incorrect.');
            return;
        }

        // Update password
        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation', 'showPasswordForm']);
        $this->emit('passwordWasUpdated', 'Password updated successfully!');
    }

    public function togglePasswordForm(): void
    {
        $this->showPasswordForm = ! $this->showPasswordForm;
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}

