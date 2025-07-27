<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\StatusEnum;
use App\Models\Comment;
use App\Models\Idea;
use App\Notifications\CommentAdded;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class AddReply extends Component
{
    public Idea $idea;
    public Comment $parentComment;
    public string $reply = '';

    protected array $rules = [
        'reply' => 'required|min:4',
    ];

    public function mount(Idea $idea, Comment $parentComment): void
    {
        $this->idea = $idea;
        $this->parentComment = $parentComment;
    }

    public function addReply(): void
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->validate();

        $newReply = Comment::create([
            'user_id' => auth()->id(),
            'status_id' => StatusEnum::Open,
            'idea_id' => $this->idea->id,
            'parent_id' => $this->parentComment->id,
            'body' => $this->reply,
        ]);

        $this->reset('reply');

        $this->idea->user->notify(new CommentAdded($newReply));


        $this->emit('replyWasAdded', 'Reply was posted!');
    }

    public function render()
    {
        return view('livewire.add-reply');
    }
}
