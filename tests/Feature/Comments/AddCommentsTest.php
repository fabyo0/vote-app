<?php

namespace Feature\Comments;

use App\Http\Livewire\AddComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use App\Notifications\CommentAdded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AddCommentsTest extends TestCase
{
    use RefreshDatabase;

    /* @test */
    public function test_add_comments_livewire_component_renders()
    {
        $idea = Idea::factory()->create();

        $response = $this->get(route('idea.show', $idea));

        $response->assertSeeLivewire('add-comment');
    }

    /* @test */
    public function test_idea_comments_form_render_when_user_is_logged_in()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create();

        $response = $this->actingAs($user)->get(route('idea.show', $idea));
        $response->assertSee('Share your thoughts');
    }

    /* @test */
    public function test_idea_comments_form_does_not_render_when_user_is_logged_out()
    {
        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertSee('Please login or create an account to post a comment');
    }

    public function test_add_comment_form_validation_works()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create();

        Livewire::actingAs($user)
            ->test(AddComment::class, [
                'idea' => $idea,
            ])
            ->set('comment', '')
            ->call('addComment')
            ->assertHasErrors('comment')
            ->set('comment', 'abc')
            ->call('addComment')
            ->assertHasErrors('comment');
    }

    public function test_add_comment_form_works()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create();

        \Notification::fake();

        \Notification::assertNothingSent();

        Livewire::actingAs($user)
            ->test(AddComment::class, [
                'idea' => $idea,
            ])
            ->set('comment', 'This first comment')
            ->call('addComment')
            ->assertEmitted('commentWasAdded');

        \Notification::assertSentTo(
            [$idea->user], CommentAdded::class
        );

        $this->equalTo(1, Comment::count());
        $this->equalTo('This first comment', $idea->comments->first()->body);
    }

    /** @test */
    public function test_comments_pagination_works()
    {
        $idea = Idea::factory()->create();

        $commentOne = Comment::factory()->create([
            'idea_id' => $idea,
        ]);

        Comment::factory($commentOne->getPerPage())->create([
            'idea_id' => $idea->id,
        ]);

        $response = $this->get(route('idea.show', $idea));

        $response->assertSee($commentOne->body);
        $response->assertDontSee(Comment::find(Comment::count())->body);

        $response = $this->get(route('idea.show', [
            'idea' => $idea,
            'page' => 2,
        ]));

        $response->assertDontSee($commentOne->body);
        $response->assertSee(Comment::find(Comment::count())->body);
    }
}
