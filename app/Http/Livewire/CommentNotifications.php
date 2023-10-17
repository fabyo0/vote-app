<?php

namespace App\Http\Livewire;

use Livewire\Component;
use function Symfony\Component\Translation\t;

class CommentNotifications extends Component
{

    public $notifications;
    public $notificationCount;

    public $isLoading;

    public const NOTIFICATION_THRESHOLD = 20;

    protected $listeners = ['getNotifications'];


    public function mount(): void
    {
        $this->notifications = collect([]);
        $this->getNotificationCount();

        $this->isLoading = true;
    }

    public function getNotificationCount(): void
    {
        sleep(5);
        $this->notificationCount = auth()->user()->unreadNotifications()->count();

        if ($this->notificationCount > self::NOTIFICATION_THRESHOLD) {
            $this->notificationCount = self::NOTIFICATION_THRESHOLD . '+';
        }
    }

    public function getNotifications(): void
    {
        $this->notifications = auth()->user()->unreadNotifications()
            ->take(self::NOTIFICATION_THRESHOLD)
            ->latest()
            ->get();

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.comment-notifications');
    }
}
