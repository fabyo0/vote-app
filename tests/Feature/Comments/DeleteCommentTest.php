<?php

namespace Feature\Comments;

use App\Http\Livewire\EditIdea;
use App\Http\Livewire\IdeaComment;
use App\Http\Livewire\IdeaShow;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Http\Livewire\DeleteComment;

class DeleteCommentTest extends TestCase
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

    /** @test */
    public function test_show_delete_idea_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('edit-comment');
    }

    /** @test */
    public function test_does_not_show_delete_idea_livewire_component_when_user_has_authorization()
    {
        User::factory()->create();

        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('edit-comment');
    }


    public function test_delete_comment_is_set_correctly_when_user_clicks_it_from_user()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id,
            'body' => 'My first comment'
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->assertEmitted('deleteCommentWasSet');
    }


    /** @test */
    public function test_deleting_a_comment_works_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment')
            ->assertEmitted('commentWasDeleted');

        $this->assertEquals(0, Comment::count());
    }


    /** @test */
    public function test_deleting_a_comment_does_not_work_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(DeleteComment::class)
            ->call('setDeleteComment', $comment->id)
            ->call('deleteComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /* @test */
    public function test_deleting_a_comment_shows_on_menu_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(IdeaComment::class,[
               'comment' => $comment,
                'ideaUserID' => $idea->user_id
            ])
            ->assertSee('Delete Comment');
    }


    /* @test */
    public function test_deleting_a_does_not_comment_shows_on_menu_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(IdeaComment::class,[
                'comment' => $comment,
                'ideaUserID' => $idea->user_id
            ])
            ->assertDontSee('Delete Comment');
    }
}
