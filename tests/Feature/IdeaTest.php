<?php

namespace Tests\Feature;

use App\Exceptions\DuplicateVoteException;
use App\Exceptions\VoteNotFoundException;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IdeaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_can_check_if_idea_is_voted_for_by_user()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
            'classes' => 'bg-gray-200'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea'
        ]);

        Vote::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id
        ]);

        $this->assertTrue($idea->isVotedByUser($user));
    }


    public function test_user_can_vote_for_idea()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
            'classes' => 'bg-gray-200'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea'
        ]);

        $this->assertFalse($idea->isVotedByUser($user));
        $idea->vote($user);
        $this->assertTrue($idea->isVotedByUser($user));
    }

    public function test_user_can_remove_vote_for_idea()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
            'classes' => 'bg-gray-200'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea'
        ]);

        Vote::create([
            'user_id' => $user->id,
            'idea_id' => $idea->id
        ]);

        $this->assertTrue($idea->isVotedByUser($user));
        $idea->removeVote($user);
        $this->assertFalse($idea->isVotedByUser($user));
    }


    public function test_removing_a_vote_that_doesnt_exist_throws_exception()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open', 'classes' => 'bg-gray-200']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $this->expectException(VoteNotFoundException::class);

        $idea->removeVote($user);
    }

}
