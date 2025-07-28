<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class CreateIdea extends Component
{
    public $title;
    public $category = 1;
    public $description;

    protected $rules = [
        'title' => 'required|string|min:4',
        'category' => 'required|integer|exists:categories,id',
        'description' => 'required|min:4|string',
    ];

    public function createIdea(): void
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
                'description' => $this->description,
            ]);

            $this->reset(['title', 'description']);
            $this->category = 1;

            $this->emit('ideaWasCreated', 'Idea was added successfully!');

            return;
        }
        abort(Response::HTTP_FORBIDDEN);
    }

    public function render()
    {
        return view('livewire.create-idea', [
            'categories' => Category::query()->select('id', 'name')->get(),
        ]);
    }
}
