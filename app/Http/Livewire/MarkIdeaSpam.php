<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkIdeaSpam extends Component
{

    public $idea;

    public function markAsSpam()
    {
        abort_if(auth()->guest(), Response::HTTP_FORBIDDEN);

        /*if (auth()->guest() ) {
            abort(Response::HTTP_FORBIDDEN);
        }*/

        $this->idea->spam_reports++;
        $this->idea->save();

        $this->emit('ideaWasMarkedAsSpam');
    }

    public function render()
    {
        return view('livewire.mark-idea-spam');
    }
}
