<?php

namespace Feature\Jobs;

use App\Jobs\NotifyAllVotes;
use App\Mail\IdeaStatusUpdatedMailable;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotifyAllVotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_email_all_voters()
    {
        $user = User::factory()->create([
            'email' => 'emredikmen002@gmail.com',
        ]);

        $userB = User::factory()->create([
            'email' => 'user@user.com',
        ]);

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusConsidering->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        Vote::create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        Vote::create([
            'idea_id' => $idea->id,
            'user_id' => $userB->id,
        ]);

        Mail::fake();

        NotifyAllVotes::dispatch($idea);

        Mail::assertQueued(IdeaStatusUpdatedMailable::class, function ($email) {
            return $email->hasTo('emredikmen002@gmail.com')
                && $email->build()->subject === "An idea you voted for has a new status";
        });
    }
}
