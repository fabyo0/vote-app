<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class AdminSettings extends Component
{
    public $notificationChannels;

    protected $rules = [
        'notificationChannels' => 'required|string',
    ];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->notificationChannels = Setting::get('notification_channels', 'database,broadcast');
    }

    public function saveSettings(): void
    {
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->validate();

        Setting::set('notification_channels', $this->notificationChannels);

        $this->emit('settingsUpdated', 'Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.admin-settings');
    }
}