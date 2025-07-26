<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class DeleteComment extends Component
{
    public ?Comment $comment;

    public $body;

    protected $listeners = [
        'setDeleteComment',
    ];

    public function setDeleteComment($commentId): void
    {
        $this->comment = Comment::query()->find($commentId);

        $this->emit('deleteCommentWasSet');
    }

    public function deleteComment(): void
    {
        if (auth()->guest() || auth()->user()->cannot('delete', $this->comment)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        Comment::destroy($this->comment->id);

        // Create empty comment
        $this->comment = Comment::query()->make();

        $this->emit('commentWasDeleted', 'Comment was deleted');
    }

    public function render()
    {
        return view('livewire.delete-comment');
    }
}
