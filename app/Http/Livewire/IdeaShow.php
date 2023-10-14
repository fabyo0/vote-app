<?php

namespace App\Http\Livewire;

use App\Exceptions\DuplicateVoteException;
use App\Exceptions\VoteNotFoundException;
use App\Models\Idea;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class IdeaShow extends Component
{
    public $idea;

    public $votesCount;
    public $hasVoted;


    protected $listeners = [
        'statusWasUpdating' => '$refresh',
        'ideaWasUpdated' => '$refresh',
        'ideaWasMarkedAsSpam' => '$refresh',
        'ideaWasMarkedAsNotSpam' => '$refresh',
        'commentWasAdded' => '$refresh',
        'commentWasDeleted' => '$refresh',
    ];

    public function mount(Idea $idea, $votesCount): void
    {
        $this->idea = $idea;
        $this->votesCount = $votesCount;
        $this->hasVoted = $idea->isVotedByUser(auth()->user());
    }

    public function statusWasUpdating(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasUpdating(): void
    {
        $this->idea->refresh();
    }

    public function commentWasDeleted(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsSpam(): void
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsNotSpam(): void
    {
        $this->idea->refresh();
    }

    public function vote()
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        try {
            if ($this->hasVoted) {
                $this->idea->removeVote(auth()->user());
                $this->updateVoteCountAndType(-1, false);
            } else {
                $this->idea->vote(auth()->user());
                $this->updateVoteCountAndType(1, true);
            }
        } catch (DuplicateVoteException|VoteNotFoundException $exception) {
            return $exception->getMessage();
        }
    }


    private function updateVoteCountAndType(int $change, bool $hasVoted): void
    {
        $this->votesCount += $change;
        $this->hasVoted = $hasVoted;
    }

    public function render()
    {
        return view('livewire.idea-show');
    }
}
