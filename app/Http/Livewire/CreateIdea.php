<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\Response;

class CreateIdea extends Component
{
    use WithFileUploads;

    public string $title = '';
    public $category = 1;
    public string $description = '';
    public $images = [];
    public $temporaryImages = [];

    protected array $rules = [
        'title' => 'required|string|min:4',
        'category' => 'required|integer|exists:categories,id',
        'description' => 'required|min:4|string',
        'images.*' => 'nullable|image|max:5120', // Max 5MB per image
    ];

    protected $messages = [
        'images.*.image' => 'You can only upload image files.',
        'images.*.max' => 'Each image must be no larger than 5MB.',
    ];

    public function updatedImages(): void
    {
        $this->validateOnly('images.*');

        foreach ($this->images as $image) {
            if ( ! in_array($image->getClientOriginalName(), array_column($this->temporaryImages, 'name'))) {
                $this->temporaryImages[] = [
                    'name' => $image->getClientOriginalName(),
                    'size' => $this->formatFileSize($image->getSize()),
                    'url' => $image->temporaryUrl(),
                ];
            }
        }
    }

    public function removeImage($index): void
    {
        unset($this->temporaryImages[$index], $this->images[$index]);


        $this->temporaryImages = array_values($this->temporaryImages);
        $this->images = array_values($this->images);
    }

    public function createIdea(): void
    {
        // Auth Check
        if (auth()->check()) {
            // validate
            $this->validate();

            $idea = Idea::create([
                'user_id' => Auth::id(),
                'category_id' => $this->category,
                'status_id' => 1,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            if ( ! empty($this->images)) {
                foreach ($this->images as $image) {
                    $idea->addMediaFromStream($image->readStream())
                        ->usingName($image->getClientOriginalName())
                        ->usingFileName($image->getClientOriginalName())
                        ->toMediaCollection('images');
                }
            }

            $this->reset(['title', 'description', 'images', 'temporaryImages']);
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

    private function formatFileSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return round($bytes / 1048576, 2) . ' MB';

    }
}
