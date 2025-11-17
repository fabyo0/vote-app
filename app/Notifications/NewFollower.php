<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * @var \App\Models\User
     */
    public $follower;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     */
    public function via($notifiable): array
    {
        $channels = Setting::get('notification_channels', 'database,broadcast');
        return array_map(trim(...), explode(',', $channels));
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Voting: You have a new follower')
            ->markdown('mail.new-follower', [
                'follower' => $this->follower,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(): array
    {
        return [
            'follower_id' => $this->follower->id,
            'follower_username' => $this->follower->username,
            'follower_name' => $this->follower->name,
            'follower_avatar' => $this->follower->getAvatar(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toBroadcast($notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new BroadcastMessage([
            'follower_id' => $this->follower->id,
            'follower_username' => $this->follower->username,
            'follower_name' => $this->follower->name,
            'follower_avatar' => $this->follower->getAvatar(),
            'type' => \App\Notifications\NewFollower::class,
        ]);
    }
}
