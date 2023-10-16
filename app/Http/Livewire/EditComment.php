<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class EditComment extends Component
{
    public ?Comment $comment;

    public $body;

    protected $listeners = [
        'setEditComment'
    ];

    protected $rules = [
        'body' => 'required|min:4'
    ];

    public function setEditComment($commentId): void
    {
        $this->comment = Comment::query()->findOrFail($commentId);
        $this->body = $this->comment->body;

        $this->emit('editCommentWasSet');
    }


    public function updateComment(): void
    {
        // Validate
        $this->validate();

        // Authorization
        if (auth()->guest() || auth()->user()->cannot('update', $this->comment)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->comment->body = $this->body;
        $this->comment->update();

        $this->emit('commentWasUpdated', 'Comment was update!');
    }

    public function render()
    {
        return view('livewire.edit-comment');
    }
}
