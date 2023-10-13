<?php

namespace Feature\Comments;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCommentsTest extends TestCase
{

    use RefreshDatabase;

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

    public function test_idea_comments_livewire_component_renders()
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comments');
    }

    public function test_idea_comment_livewire_component_renders()
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('idea-comment');
    }

    public function test_no_comments_shows_appropriate_message()
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertSee('No comments yet...');
    }

    public function test_list_comments_shows_on_idea_page()
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is fist comment'
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is second comment'
        ]);

        $this->get(route('idea.show', $idea))
            ->assertSeeInOrder(['This is fist comment', 'This is second comment'])
            ->assertSee('2 Comments');
    }


    public function test_list_comments_counts_shows_correctly_on_index_page()
    {
        $idea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is fist comment'
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is second comment'
        ]);

        $this->get(route('idea.index'))
            ->assertSee('2 Comments');
    }

    /* @test */
    public function test_op_badge_shows_if_author_idea_comments_on_idea()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is fist comment'
        ]);

        Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is second comment'
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSee('OP');
    }

}
