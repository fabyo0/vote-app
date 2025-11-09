<?php

namespace Feature\Comments;

use App\Http\Livewire\EditComment;
use App\Http\Livewire\IdeaComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditCommentTest extends TestCase
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
    public function test_show_edit_comment_livewire_component_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('edit-comment');
    }

    /** @test */
    public function test_does_not_show_edit_idea_livewire_component_when_user_has_authorization()
    {
        User::factory()->create();

        $idea = Idea::factory()->create();

        $this->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('edit-comment');
    }

    public function test_edit_comment_is_set_correctly_when_user_clicks_it_from_user()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id,
            'body' => 'My first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->assertSet('body', 'My first comment')
            ->assertSet('comment.id', $comment->id)
            ->assertEmitted('editCommentWasSet');
    }

    /** @test */
    public function test_edit_a_comment_works_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', 'Updated comment body')
            ->call('updateComment')
            ->assertEmitted('commentWasUpdated', 'Comment was update!');

        $this->assertEquals('Updated comment body', $comment->fresh()->body);
    }

    public function test_edit_comment_form_validation_work()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', '')
            ->call('updateComment')
            ->assertHasErrors(['body'])
            ->set('body', 'ab')
            ->call('updateComment')
            ->assertHasErrors(['body']);

    }

    /** @test */
    public function test_edit_a_comment_does_not_work_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->call('updateComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /* @test */
    public function test_editing_a_comment_shows_on_menu_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class, [
                'comment' => $comment,
                'ideaUserID' => $idea->user_id,
            ])
            ->assertSee('Edit Comment');
    }

    /* @test */
    public function test_edit_a_does_not_comment_shows_on_menu_when_user_has_authorization()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(IdeaComment::class, [
                'comment' => $comment,
                'ideaUserID' => $idea->user_id,
            ])
            ->assertDontSee('Edit Comment');
    }

    /** @test */
    public function test_guest_user_cannot_update_comment()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', 'Updated comment')
            ->call('updateComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals('This is my first comment', $comment->fresh()->body);
    }

    /** @test */
    public function test_edit_comment_component_can_render()
    {
        Livewire::test(EditComment::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.edit-comment');
    }

    /** @test */
    public function test_set_edit_comment_fails_when_comment_not_found()
    {
        $user = User::factory()->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', 99999);
    }

    /** @test */
    public function test_update_comment_with_minimum_length_validation()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create();

        $comment = Comment::factory()->create([
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'body' => 'This is my first comment',
        ]);

        Livewire::actingAs($user)
            ->test(EditComment::class)
            ->call('setEditComment', $comment->id)
            ->set('body', 'abc') // Less than 4 characters
            ->call('updateComment')
            ->assertHasErrors(['body']);
    }
}
