<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class EditIdea extends Component
{

    public $idea;
    public $title;
    public $category;
    public $description;


    protected $rules = [
        'title' => 'required|string|min:4',
        'category' => 'required|integer|exists:categories,id',
        'description' => 'required|min:4|string',
    ];

    public function updateIdea(): void
    {
        $this->validate();

        // Authorization
        if (auth()->guest() || auth()->user()->cannot('update', $this->idea)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        // Update Idea
        $this->idea->update([
            'title' => $this->title,
            'category_id' => $this->category,
            'description' => $this->description
        ]);

        $this->emit('ideaWasUpdated');
    }

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
        $this->title = $idea->title;
        $this->category = $idea->category_id;
        $this->description = $idea->description;
    }

    public function render()
    {
        return view('livewire.edit-idea', [
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }
}
