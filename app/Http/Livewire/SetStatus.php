<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Jobs\NotifyAllVotes;
use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class SetStatus extends Component
{
    public $idea;

    public $status;

    public $notifyAllVoters;

    public $comment;

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
        $this->status = $this->idea->status_id;
    }

    public function setStatus(): void
    {
        // admin check
        $this->authorizeAdmin();

        $this->idea->update(['status_id' => $this->status]);

        if ($this->notifyAllVoters) {
            //$this->notifyAllVoters();
            NotifyAllVotes::dispatch($this->idea);
        }

        Comment::create([
            'user_id' => auth()->id(),
            'status_id' => $this->status,
            'idea_id' => $this->idea->id,
            'body' => $this->comment ?? 'No comment was added',
            'is_status_update' => 1,
        ]);

        $this->reset('comment');

        //Emit Event
        $this->emit('statusWasUpdating', 'Status was updated successfully!');
    }

    public function render()
    {
        return view('livewire.set-status');
    }

    protected function authorizeAdmin(): void
    {
        if ( ! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
