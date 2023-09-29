<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Idea;
use Illuminate\View\View;

class IdeaController extends Controller
{

    public function index(): View
    {
        return view('idea.index', [
            'ideas' => Idea::query()
                ->with(['category', 'user', 'status'])
                ->withCount('votes')
                ->latest()
                ->simplePaginate(Idea::PAGINATION_COUNT)
        ]);
    }

    public function create()
    {
        //
    }


    public function store(StoreIdeaRequest $request)
    {
        //
    }


    public function show(Idea $idea)
    {
        return view('idea.show', [
            'idea' => $idea,
            'votesCount' => $idea->votes()
                ->count()
        ]);
    }


    public function edit(Idea $idea)
    {
        //
    }


    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        //
    }


    public function destroy(Idea $idea)
    {
        //
    }
}
