<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IdeaStatus;
use App\Enums\StatusEnum;
use App\Models\Idea;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        return response()->view('idea.index', [
            'ideas' => Idea::query()
                ->when(IdeaStatus::All !== $request->status, fn($query) => $query->where('status_id', StatusEnum::Open))
                ->addSelect(['voted_by_user' => Vote::query()->select('ideas.id')
                    ->where('user_id', Auth::id())
                    ->whereColumn('idea_id', 'ideas.id'),
                ])
                ->with(['category', 'user', 'status', 'comments'])
                ->withCount(['votes', 'comments'])
                ->latest()
                ->simplePaginate(),
        ]);

    }

    public function show(Idea $idea)
    {
        return view('idea.show', [
            'idea' => $idea,
            'votesCount' => $idea->votes()
                ->count(),
            'backUrl' => url()->previous() !== url()->full()
                ? url()->previous()
                : route('idea.show', $idea),
        ]);
    }
}
