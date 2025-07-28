<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class DeleteIdea extends Component
{
    public Idea $idea;

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function deleteIdea()
    {
        if (auth()->guest() || auth()->user()->cannot('delete', $this->idea)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        Idea::destroy($this->idea->id);

        session()->flash('success_message', 'Idea was deleted successfully!');

        return Redirect::route('idea.index');
    }

    public function render()
    {
        return view('livewire.delete-idea');
    }
}
