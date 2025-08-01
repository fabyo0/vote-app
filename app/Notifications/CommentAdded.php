<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAdded extends Notification
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Voting: A comment was posted on your idea ')
            ->markdown('mail.comment-added', [
                'comment' => $this->comment,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'user_avatar' => $this->comment->user->getAvatar(),
            'user_name' => $this->comment->user->name,
            'idea_id' => $this->comment->idea->id,
            'idea_slug' => $this->comment->idea->slug,
            'idea_title' => $this->comment->idea->title,
        ];
    }
}
