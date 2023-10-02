<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class IdeaIndex extends Component
{
    public $idea;

    public int $votesCount;

    public $hasVoted;

    public function mount(Idea $idea, $votesCount): void
    {
        $this->idea = $idea;
        $this->votesCount = $votesCount;
        $this->hasVoted = $idea->voted_by_user;
    }

    public function vote()
    {
        if (!auth()->check()) {
            return Redirect::route('login');
        }

        if ($this->hasVoted) {
            $this->idea->removeVote(auth()->user());

            $this->updateVoteCountAndType(-1, false);
        } else {
            $this->idea->vote(auth()->user());

            $this->updateVoteCountAndType(1, true);
        }
    }

    private function updateVoteCountAndType(int $change, bool $hasVoted): void
    {
        $this->votesCount += $change;
        $this->hasVoted = $hasVoted;
    }

    public function render()
    {
        return view('livewire.idea-index');
    }
}
