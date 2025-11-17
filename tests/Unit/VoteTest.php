<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Idea $idea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->idea = Idea::factory()->create();
    }

    /** @test */
    public function test_vote_can_be_created()
    {
        $vote = Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        $this->assertDatabaseHas('votes', [
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);
    }

    /** @test */
    public function test_vote_fillable_attributes()
    {
        $vote = new Vote();
        $vote->fill([
            'user_id' => 1,
            'idea_id' => 1,
        ]);

        $this->assertEquals(1, $vote->user_id);
        $this->assertEquals(1, $vote->idea_id);
    }

    /** @test */
    public function test_vote_can_be_deleted()
    {
        $vote = Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        $voteId = $vote->id;
        $vote->delete();

        $this->assertDatabaseMissing('votes', [
            'id' => $voteId,
        ]);
    }

    /** @test */
    public function test_vote_timestamps_are_set()
    {
        $vote = Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        $this->assertNotNull($vote->created_at);
        $this->assertNotNull($vote->updated_at);
    }

    /** @test */
    public function test_user_can_vote_on_multiple_ideas()
    {
        $idea1 = Idea::factory()->create();
        $idea2 = Idea::factory()->create();

        Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $idea1->id,
        ]);

        Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $idea2->id,
        ]);

        $this->assertEquals(2, Vote::where('user_id', $this->user->id)->count());
    }

    /** @test */
    public function test_idea_can_have_multiple_votes()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Vote::factory()->create([
            'user_id' => $user1->id,
            'idea_id' => $this->idea->id,
        ]);

        Vote::factory()->create([
            'user_id' => $user2->id,
            'idea_id' => $this->idea->id,
        ]);

        $this->assertEquals(2, Vote::where('idea_id', $this->idea->id)->count());
    }

    /** @test */
    public function test_vote_prevents_duplicate_votes()
    {
        Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        // Check that only one vote exists for this user-idea combination
        $this->assertEquals(1, Vote::where([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ])->count());
    }

    /** @test */
    public function test_vote_can_be_found_by_user_and_idea()
    {
        $vote = Vote::factory()->create([
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        $foundVote = Vote::where('user_id', $this->user->id)
            ->where('idea_id', $this->idea->id)
            ->first();

        $this->assertNotNull($foundVote);
        $this->assertEquals($vote->id, $foundVote->id);
    }

    /** @test */
    public function test_multiple_votes_on_different_ideas()
    {
        $idea1 = Idea::factory()->create();
        $idea2 = Idea::factory()->create();
        $idea3 = Idea::factory()->create();

        Vote::factory()->create(['user_id' => $this->user->id, 'idea_id' => $idea1->id]);
        Vote::factory()->create(['user_id' => $this->user->id, 'idea_id' => $idea2->id]);
        Vote::factory()->create(['user_id' => $this->user->id, 'idea_id' => $idea3->id]);

        $voteCount = Vote::where('user_id', $this->user->id)->count();

        $this->assertEquals(3, $voteCount);
    }
}
