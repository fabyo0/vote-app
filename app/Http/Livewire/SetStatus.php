<?php

namespace App\Http\Livewire;

use App\Enums\StatusEnum;
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

    /*  protected $rules = [
          'status' => 'required'
      ];*/


    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
        $this->status = $this->idea->status_id;
    }

    public function setStatus(): void
    {
        // $this->validate();
        // admin check
        $this->authorizeAdmin();
        /*    $this->idea->status_id = $this->status;
            $this->idea->save();
        */
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
            'is_status_update' => 1
        ]);


        //$this->reset('comment');

        //Emit Event
        $this->emit('statusWasUpdating', 'Status was updated successfully!');
    }

    protected function authorizeAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function render()
    {
        return view('livewire.set-status');
    }
}
