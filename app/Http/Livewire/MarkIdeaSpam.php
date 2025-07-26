<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkIdeaSpam extends Component
{
    public Idea $idea;

    public function markAsSpam(): void
    {
        $user = auth()->user();

        if ( ! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->id === $this->idea->user_id) {
            $this->emit('spamActionFailed', 'You cannot mark your own idea as spam.');
            return;
        }

        $alreadyReported = session()->get('spam_reports', []);

        if (in_array($this->idea->id, $alreadyReported)) {
            $this->emit('spamActionFailed', 'You have already marked this idea as spam.');
            return;
        }

        $this->idea->increment('spam_reports');

        session()->put('spam_reports', array_merge($alreadyReported, [$this->idea->id]));


        $this->emit('ideaWasMarkedAsSpam', 'Idea was marked as spam!');
    }

    public function render()
    {
        return view('livewire.mark-idea-spam');
    }
}
