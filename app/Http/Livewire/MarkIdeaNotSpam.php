<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkIdeaNotSpam extends Component
{
    public $idea;

    public function markAsNotSpam(): void
    {
        abort_if(auth()->guest() || !auth()->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $this->idea->spam_reports = 0;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsNotSpam');
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function render()
    {
        return view('livewire.mark-idea-not-spam');
    }
}
