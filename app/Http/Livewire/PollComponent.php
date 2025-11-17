<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Poll;
use App\Models\PollVote;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class PollComponent extends Component
{
    public Poll $poll;
    public Idea $idea;
    public $selectedOption;
    public $showResults = false;
    public $voteCounts = [];

    public function mount(Poll $poll, Idea $idea): void
    {
        $this->poll = $poll;
        $this->idea = $idea;
        
        if (auth()->check()) {
            $userVote = $this->poll->votes()->where('user_id', auth()->id())->first();
            if ($userVote) {
                $this->selectedOption = $userVote->option_index;
                $this->showResults = true;
            }
        }

        if ($this->showResults || !$this->poll->canVote()) {
            $this->voteCounts = $this->poll->getVoteCounts();
        }
    }

    public function vote(): void
    {
        if (auth()->guest()) {
            session()->flash('error_message', 'Please login to vote in polls.');
            return;
        }

        if (!$this->poll->canVote()) {
            session()->flash('error_message', 'This poll is no longer active.');
            return;
        }

        if ($this->selectedOption === null) {
            session()->flash('error_message', 'Please select an option.');
            return;
        }

        if ($this->poll->hasVotedByUser(auth()->user())) {
            // Update existing vote
            $this->poll->votes()
                ->where('user_id', auth()->id())
                ->update(['option_index' => $this->selectedOption]);
        } else {
            // Create new vote
            PollVote::create([
                'poll_id' => $this->poll->id,
                'user_id' => auth()->id(),
                'option_index' => $this->selectedOption,
            ]);
        }

        $this->showResults = true;
        $this->voteCounts = $this->poll->fresh()->getVoteCounts();
        
        $this->emit('pollWasVoted');
    }

    public function showResults(): void
    {
        $this->showResults = true;
        $this->voteCounts = $this->poll->getVoteCounts();
    }

    public function render()
    {
        return view('livewire.poll-component');
    }
}
