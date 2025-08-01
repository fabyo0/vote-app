<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Exceptions\DuplicateVoteException;
use App\Exceptions\VoteNotFoundException;
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
        if ( ! auth()->check()) {
            return Redirect::route('login');
        }

        if ($this->hasVoted) {
            try {
                $this->idea->removeVote(auth()->user());
            } catch (VoteNotFoundException $exception) {
                return $exception->getMessage();
            }

            $this->updateVoteCountAndType(-1, false);
        } else {
            try {
                $this->idea->vote(auth()->user());
            } catch (DuplicateVoteException $exception) {
                return $exception->getMessage();
            }
            $this->updateVoteCountAndType(1, true);
        }
    }

    public function render()
    {
        return view('livewire.idea-index');
    }

    private function updateVoteCountAndType(int $change, bool $hasVoted): void
    {
        $this->votesCount += $change;
        $this->hasVoted = $hasVoted;
    }
}
