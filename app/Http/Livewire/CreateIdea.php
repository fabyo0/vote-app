<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class CreateIdea extends Component
{
    public $title;

    public $category = 1;

    public $description;

    protected $rules = [
        'title' => 'required|string|min:4',
        'category' => 'required|integer',
        'description' => 'required|min:4|string'
    ];

    public function createIdea()
    {
        // Auth Check
        if (auth()->check()) {
            // validate
            $this->validate();

            Idea::create([
                'user_id' => Auth::id(),
                'category_id' => $this->category,
                'status_id' => 1,
                'title' => $this->title,
                'description' => $this->description
            ]);

            session()->flash('success_message', 'Idea was added successfully.');

            $this->reset();

            return Redirect::route('idea.index');
        }
        abort(Response::HTTP_FORBIDDEN);
    }

    public function render()
    {
        return view('livewire.create-idea', [
            'categories' => Category::query()->select('id', 'name')->get()
        ]);
    }
}
