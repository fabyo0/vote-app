<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserProfileShow extends Component
{
    use WithPagination;

    public User $user;
    public $activeTab = 'ideas'; // 'ideas', 'comments', 'followers', 'following'
    public $isFollowing = false;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->checkFollowingStatus();
    }

    public function checkFollowingStatus(): void
    {
        if (auth()->check()) {
            $this->isFollowing = auth()->user()->isFollowing($this->user);
        }
    }

    public function toggleFollow()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        if (auth()->id() === $this->user->id) {
            return; // Can't follow yourself
        }

        $currentUser = auth()->user();

        if ($this->isFollowing) {
            $currentUser->unfollow($this->user);
            $this->isFollowing = false;
        } else {
            $currentUser->follow($this->user);
            $this->isFollowing = true;
        }

        $this->user->refresh();
    }

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $ideas = null;
        $comments = null;

        if ($this->activeTab === 'ideas') {
            $ideas = Idea::where('user_id', $this->user->id)
                ->with(['category', 'status', 'user'])
                ->withCount('votes')
                ->latest()
                ->paginate(10);
        } else {
            $comments = Comment::where('user_id', $this->user->id)
                ->whereNull('parent_id') // Only top-level comments
                ->with(['idea', 'user', 'status'])
                ->latest()
                ->paginate(10);
        }

        $followers = null;
        $following = null;

        if ($this->activeTab === 'followers') {
            $followers = $this->user->followers()
                ->withCount(['ideas', 'comments'])
                ->paginate(12);
        } elseif ($this->activeTab === 'following') {
            $following = $this->user->following()
                ->withCount(['ideas', 'comments'])
                ->paginate(12);
        }

        return view('livewire.user-profile-show', [
            'ideas' => $ideas,
            'comments' => $comments,
            'followers' => $followers,
            'following' => $following,
            'ideasCount' => Idea::where('user_id', $this->user->id)->count(),
            'commentsCount' => Comment::where('user_id', $this->user->id)->count(),
            'followersCount' => $this->user->followers()->count(),
            'followingCount' => $this->user->following()->count(),
        ]);
    }
}

