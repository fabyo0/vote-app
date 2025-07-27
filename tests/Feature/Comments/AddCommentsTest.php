<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\StatusEnum;
use App\Http\Livewire\AddComment;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use App\Notifications\CommentAdded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AddCommentsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $ideaAuthor;
    private Idea $idea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->ideaAuthor = User::factory()->create();
        $this->idea = Idea::factory()->create(['user_id' => $this->ideaAuthor->id]);
    }

    /** @test */
    public function test_add_comments_livewire_component_renders()
    {
        $response = $this->get(route('idea.show', $this->idea));

        $response->assertSeeLivewire('add-comment');
    }

    /** @test */
    public function test_add_comment_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->assertStatus(200)
            ->assertViewIs('livewire.add-comment');
    }

    /** @test */
    public function test_idea_comments_form_render_when_user_is_logged_in()
    {
        $response = $this->actingAs($this->user)->get(route('idea.show', $this->idea));
        $response->assertSee('Share your thoughts');
    }

    /** @test */
    public function test_idea_comments_form_does_not_render_when_user_is_logged_out()
    {
        $this->get(route('idea.show', $this->idea))
            ->assertSee('Please login or create an account to post a comment');
    }

    /** @test */
    public function test_comment_property_can_be_updated()
    {
        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->assertSet('comment', $commentText);
    }

    /** @test */
    public function test_guest_user_cannot_add_comment()
    {
        Livewire::test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', 'This is a test comment')
            ->call('addComment')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals(0, Comment::count());
    }

    /** @test */
    public function test_add_comment_form_validation_works()
    {
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', '')
            ->call('addComment')
            ->assertHasErrors(['comment' => 'required'])
            ->set('comment', 'abc')
            ->call('addComment')
            ->assertHasErrors(['comment' => 'min']);
    }

    /** @test */
    public function test_comment_minimum_length_validation()
    {
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', 'ab') // 2 characters, less than minimum 4
            ->call('addComment')
            ->assertHasErrors(['comment' => 'min']);
    }

    /** @test */
    public function test_comment_with_minimum_valid_length_passes_validation()
    {
        $commentText = 'abcd'; // exactly 4 characters, minimum required

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'body' => $commentText,
        ]);
    }

    /** @test */
    public function test_add_comment_form_works()
    {
        Notification::fake();

        $commentText = 'This first comment';

        $this->assertEquals(0, Comment::count());

        $component = Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $this->assertEquals(1, Comment::count());

        $createdComment = Comment::first();
        $this->assertEquals($commentText, $createdComment->body);
        $this->assertEquals($this->user->id, $createdComment->user_id);
        $this->assertEquals($this->idea->id, $createdComment->idea_id);
        $this->assertNull($createdComment->parent_id);

        $this->assertNull($component->get('comment'));

        Notification::assertSentTo([$this->ideaAuthor], CommentAdded::class);
    }

    /** @test */
    public function test_comment_field_is_reset_after_successful_submission()
    {
        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment')
            ->assertSet('comment', '');
    }

    /** @test */
    public function test_created_comment_has_correct_status()
    {
        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $comment = Comment::where('body', $commentText)->first();

        $this->assertNotNull($comment);
        $this->assertEquals(StatusEnum::Open->value, $comment->status_id);
    }

    /** @test */
    public function test_created_comment_belongs_to_authenticated_user()
    {
        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $comment = Comment::where('body', $commentText)->first();

        $this->assertNotNull($comment);
        $this->assertEquals($this->user->id, $comment->user_id);
    }

    /** @test */
    public function test_notification_is_sent_to_idea_author_when_comment_is_added()
    {
        Notification::fake();

        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        Notification::assertSentTo($this->ideaAuthor, CommentAdded::class);
    }

    /** @test */
    public function test_notification_contains_correct_comment_data()
    {
        Notification::fake();

        $commentText = 'This is a test comment for notification';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $comment = Comment::where('body', $commentText)->first();

        Notification::assertSentTo($this->ideaAuthor, CommentAdded::class, function ($notification) use ($comment) {
            return $notification->comment->id === $comment->id;
        });
    }

    /** @test */
    public function test_comment_was_added_event_is_emitted_after_successful_submission()
    {
        $commentText = 'This is a test comment';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment')
            ->assertEmitted('commentWasAdded', 'Comment was posted!');
    }

    /** @test */
    public function test_multiple_comments_can_be_added_to_same_idea()
    {
        $firstComment = 'First comment text';
        $secondComment = 'Second comment text';

        // Add first comment
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $firstComment)
            ->call('addComment');

        // Add second comment
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $secondComment)
            ->call('addComment');

        $this->assertDatabaseHas('comments', [
            'body' => $firstComment,
            'idea_id' => $this->idea->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $secondComment,
            'idea_id' => $this->idea->id,
        ]);

        $commentsCount = Comment::where('idea_id', $this->idea->id)->count();
        $this->assertEquals(2, $commentsCount);
    }

    /** @test */
    public function test_different_users_can_comment_on_same_idea()
    {
        $anotherUser = User::factory()->create();
        $userComment = 'Comment from first user';
        $anotherUserComment = 'Comment from second user';

        // First user comments
        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $userComment)
            ->call('addComment');

        // Second user comments
        Livewire::actingAs($anotherUser)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $anotherUserComment)
            ->call('addComment');

        $this->assertDatabaseHas('comments', [
            'body' => $userComment,
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $anotherUserComment,
            'user_id' => $anotherUser->id,
            'idea_id' => $this->idea->id,
        ]);
    }

    /** @test */
    public function test_comment_persists_in_database()
    {
        $commentText = 'This comment should persist';

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'status_id' => StatusEnum::Open,
            'idea_id' => $this->idea->id,
            'parent_id' => null,
            'body' => $commentText,
        ]);
    }

    /** @test */
    public function test_validation_errors_do_not_reset_comment_field()
    {
        $invalidComment = 'ab'; // Too short

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $invalidComment)
            ->call('addComment')
            ->assertHasErrors(['comment' => 'min'])
            ->assertSet('comment', $invalidComment); // Comment should not be reset on validation error
    }

    /** @test */
    public function test_comments_pagination_works()
    {
        $commentOne = Comment::factory()->create([
            'idea_id' => $this->idea->id,
        ]);

        Comment::factory($commentOne->getPerPage())->create([
            'idea_id' => $this->idea->id,
        ]);

        $response = $this->get(route('idea.show', $this->idea));

        $response->assertSee($commentOne->body);
        $response->assertDontSee(Comment::find(Comment::count())->body);

        $response = $this->get(route('idea.show', [
            'idea' => $this->idea,
            'page' => 2,
        ]));

        $response->assertDontSee($commentOne->body);
        $response->assertSee(Comment::find(Comment::count())->body);
    }

    /** @test */
    public function test_idea_author_can_comment_on_own_idea()
    {
        $commentText = 'Author commenting on own idea';

        Livewire::actingAs($this->ideaAuthor)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $commentText)
            ->call('addComment');

        $this->assertDatabaseHas('comments', [
            'body' => $commentText,
            'user_id' => $this->ideaAuthor->id,
            'idea_id' => $this->idea->id,
        ]);
    }

    /** @test */
    public function test_long_comment_can_be_added()
    {
        $longComment = str_repeat('This is a long comment. ', 50); // ~1000 characters

        Livewire::actingAs($this->user)
            ->test(AddComment::class, ['idea' => $this->idea])
            ->set('comment', $longComment)
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'body' => $longComment,
            'user_id' => $this->user->id,
            'idea_id' => $this->idea->id,
        ]);
    }
}
