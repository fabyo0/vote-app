<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Idea;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $ideas = Idea::query()
            ->when($request->status !== 'All', function ($query) {
                return $query->where('status_id', StatusEnum::Open);
            })
            ->addSelect(['voted_by_user' => Vote::query()->select('ideas.id')
                ->where('user_id', Auth::id())
                ->whereColumn('idea_id', 'ideas.id')
            ])
            ->with(['category', 'user', 'status'])
            ->withCount('votes')
            ->latest()
            ->simplePaginate(Idea::PAGINATION_COUNT);

        return response()->view('idea.index', [
            'ideas' => $ideas
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
