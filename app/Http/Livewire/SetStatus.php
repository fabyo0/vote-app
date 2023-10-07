<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class SetStatus extends Component
{
    public $idea;
    public $status;

    protected $rules = [
        'status' => 'required'
    ];

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
        $this->status = $this->idea->status_id;
    }

    public function setStatus(): void
    {
        $this->validate();
        // admin check
        $this->authorizeAdmin();
    /*    $this->idea->status_id = $this->status;
        $this->idea->save();
    */
        $this->idea->update(['status_id' => $this->status]);

        //Emit Event
        $this->emit('statusWasUpdating');
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
