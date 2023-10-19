<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

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
        $this->notificationCount = auth()->user()->unreadNotifications()->count();

        if ($this->notificationCount > self::NOTIFICATION_THRESHOLD) {
            $this->notificationCount = self::NOTIFICATION_THRESHOLD . '+';
        }
    }

    // Read Idea
    public function markAsRead($notificationId): void
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $notification = DatabaseNotification::findOrFail($notificationId);
        $notification->markAsRead();

        $this->scrollToComment($notification);
    }


    public function scrollToComment($notification)
    {
        $ideaId = $notification->data['idea_id'];
        $commentId = $notification->data['comment_id'];
        $ideaSlug = $notification->data['idea_slug'];

        $idea = Idea::query()->find($ideaId);
        $comment = Comment::query()->find($commentId);

        if (!$idea || !$comment) {
            $errorMessage = '';
            if (!$idea) {
                $errorMessage = 'This idea no longer exists!';
            } elseif (!$comment) {
                $errorMessage = 'This comment no longer exists!';
            }

            session()->flash('error_message', $errorMessage);
            return redirect()->route('idea.index');
        }

        $comments = $idea->comments()->pluck('id');
        $indexOfComment = $comments->search($commentId);

        if (!$indexOfComment) {
            session()->flash('error_message', 'Comment not found in the idea!');
            return redirect()->route('idea.index');
        }

        $perPage = $comment->getPerPage();
        $page = (int)($indexOfComment / $perPage) + 1;

        session()->flash('scrollToComment', $commentId);

        return redirect()->route('idea.show', [
            'idea' => $ideaSlug,
            'page' => $page,
        ]);
    }



    // All Read Ideas
    public function markAllAsRead()
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        auth()->user()->unreadNotifications->markAsRead();
        $this->getNotificationCount();
        $this->getNotifications();
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
