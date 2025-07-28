<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\StatusEnum;
use App\Models\Comment;
use App\Models\Idea;
use App\Notifications\CommentAdded;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class AddComment extends Component
{
    public $idea;
    public $comment;
    public $parentComment = null;

    protected $rules = [
        'comment' => 'required|min:4',
    ];

    public function mount(Idea $idea, ?Comment $comment = null, ?Comment $parentComment = null): void
    {
        $this->idea = $idea;
        $this->comment = $comment;
        $this->parentComment = $parentComment;
    }

    public function addComment(): void
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->validate();

        $newComment = Comment::create([
            'user_id' => auth()->id(),
            'status_id' => StatusEnum::Open,
            'idea_id' => $this->idea->id,
            'parent_id' => $this->parentComment?->id,
            'body' => $this->comment,
        ]);

        $this->reset('comment');

        $this->idea->user->notify(new CommentAdded($newComment));

        if ($this->parentComment) {
            $this->emit('replyWasAdded', 'Reply was posted!');
        } else {
            $this->emit('commentWasAdded', 'Comment was posted!');
        }
    }

    public function isReplyMode(): bool
    {
        return null !== $this->parentComment;
    }

    public function render()
    {
        return view('livewire.add-comment');
    }
}
