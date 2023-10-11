<?php

namespace App\Http\Livewire;

use App\Jobs\NotifyAllVotes;
use App\Models\Idea;
use Illuminate\Http\Response;
use Livewire\Component;

class SetStatus extends Component
{
    public $idea;
    public $status;
    public $notifyAllVoters;


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
