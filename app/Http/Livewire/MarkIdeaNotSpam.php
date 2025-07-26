<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkIdeaNotSpam extends Component
{
    public Idea $idea;

    public function markAsNotSpam(): void
    {
        abort_if(auth()->guest() || ! auth()->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $this->idea->spam_reports = 0;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsNotSpam', 'Spam counter was reset!');
    }

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function render()
    {
        return view('livewire.mark-idea-not-spam');
    }
}
