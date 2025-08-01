<?php

namespace Feature\Vote;

use App\Http\Livewire\IdeaShow;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoteShowPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_contains_idea_show_livewire_component()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        // contains idea-index contains
        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-show');
    }

    public function test_show_page_correctly_receives_votes_count()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $userB->id,
        ]);

        $this->get(route('idea.show', $idea))
            ->assertViewHas('votesCount', 2);
    }

    public function test_votes_count_correctly_on_show_page_livewire_component()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $userB->id,
        ]);

        Livewire::test(IdeaShow::class, [
            'idea' => $idea,
            'votesCount' => 5,
        ])->assertSet('votesCount', 5);
    }

    public function test_user_who_is_logged_in_shows_voted_if_idea_already_voted_for()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open'
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
        ]);

        Vote::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $userB->id,
        ]);

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 5,
            ])->assertSet('hasVoted', true)
            ->assertSee('Voted');
    }

    public function test_user_who_is_not_logged_in_is_redirected_to_login_page_when_trying_to_vote()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        Livewire::test(IdeaShow::class, [
            'idea' => $idea,
            'votesCount' => 5,
        ])
            ->call('vote')
            ->assertRedirect(route('login'));
    }

    public function test_user_who_is__logged_in_can_vote_for_idea()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        //TODO: Assert that a given where condition does not exist in the database.
        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'idea_id' => $idea->id,
        ]);

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 5,
            ])
            ->call('vote')
            ->assertSet('votesCount', 6)
            ->assertSet('hasVoted', true)
            ->assertSee('Voted');

        //TODO: Check Database
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'idea_id' => $idea->id,
        ]);
    }

    public function test_user_who_is_logged_in_can_remove_vote_for_idea()
    {
        $user = User::factory()->create();
        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $idea->vote($user);
        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'idea_id' => $idea->id,
        ]);

        $this->assertTrue($idea->isVotedByUser($user));

        Livewire::actingAs($user)
            ->test(IdeaShow::class, [
                'idea' => $idea,
                'votesCount' => 5,
            ])
            ->call('vote')
            ->assertSet('votesCount', 4)
            ->assertSet('hasVoted', false)
            ->assertSee('Vote')
            ->assertDontSee('Voted');

        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'idea_id' => $idea->id,
        ]);
        $this->assertFalse($idea->fresh()->isVotedByUser($user));
    }
}
