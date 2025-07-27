<?php

namespace Tests\Feature\Comments;

use App\Enums\StatusEnum;
use App\Http\Livewire\AddReply;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use App\Notifications\CommentAdded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AddReplyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $ideaAuthor;
    private Idea $idea;
    private Comment $parentComment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->ideaAuthor = User::factory()->create();
        $this->idea = Idea::factory()->create(['user_id' => $this->ideaAuthor->id]);

        $this->parentComment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->assertStatus(200)
            ->assertViewIs('livewire.add-reply');
    }

    public function test_reply_property_can_be_updated()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->assertSet('reply', $replyText);
    }


    public function test_guest_user_cannot_add_reply()
    {
        Livewire::test(AddReply::class, [
            'idea' => $this->idea,
            'parentComment' => $this->parentComment
        ])
            ->set('reply', 'this reply text')
            ->call('addReply')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_authenticated_user_can_add_reply_successfully()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'status_id' => StatusEnum::Open,
            'idea_id' => $this->idea->id,
            'parent_id' => $this->parentComment->id,
            'body' => $replyText,
        ]);
    }


    public function test_reply_field_is_reset_after_successful_submission()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply')
            ->assertSet('reply', '');
    }

    public function test_reply_is_required_validation()
    {
        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', '')
            ->call('addReply')
            ->assertHasErrors(['reply' => 'required']);
    }

    /** @test */
    public function test_reply_minimum_length_validation()
    {
        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', 'abc')
            ->call('addReply')
            ->assertHasErrors(['reply' => 'min']);
    }

    public function test_reply_with_minimum_valid_length_passes_validation()
    {
        $replyText = 'abcd'; // exactly 4 characters, minimum required

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('comments', [
            'body' => $replyText,
        ]);
    }

    public function test_notification_is_sent_to_idea_author_when_reply_is_added()
    {
        Notification::fake();

        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply');

        Notification::assertSentTo($this->ideaAuthor, CommentAdded::class);
    }


    /** @test */
    public function test_reply_was_added_event_is_emitted_after_successful_submission()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply')
            ->assertEmitted('replyWasAdded', 'Reply was posted!');
    }

    /** @test */
    public function test_created_reply_has_correct_parent_relationship()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply');

        $reply = Comment::where('body', $replyText)->first();

        $this->assertNotNull($reply);
        $this->assertEquals($this->parentComment->id, $reply->parent_id);
        $this->assertEquals($this->idea->id, $reply->idea_id);
    }

    /** @test */
    public function test_created_reply_has_open_status()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply');

        $reply = Comment::where('body', $replyText)->first();

        $this->assertNotNull($reply);
        $this->assertEquals(StatusEnum::Open->value, $reply->status_id);
    }

    /** @test */
    public function test_created_reply_belongs_to_authenticated_user()
    {
        $replyText = 'This is a test reply';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply');

        $reply = Comment::where('body', $replyText)->first();

        $this->assertNotNull($reply);
        $this->assertEquals($this->user->id, $reply->user_id);
    }

    /** @test */
    public function test_notification_contains_correct_reply_data()
    {
        Notification::fake();

        $replyText = 'This is a test reply for notification';

        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $replyText)
            ->call('addReply');

        $reply = Comment::where('body', $replyText)->first();

        Notification::assertSentTo($this->ideaAuthor, CommentAdded::class, function ($notification) use ($reply) {
            return $notification->comment->id === $reply->id;
        });
    }

    /** @test */
    public function test_multiple_replies_can_be_added_to_same_parent_comment()
    {
        $firstReply = 'First reply text';
        $secondReply = 'Second reply text';

        // Add first reply
        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $firstReply)
            ->call('addReply');

        // Add second reply
        Livewire::actingAs($this->user)
            ->test(AddReply::class, [
                'idea' => $this->idea,
                'parentComment' => $this->parentComment
            ])
            ->set('reply', $secondReply)
            ->call('addReply');

        $this->assertDatabaseHas('comments', [
            'body' => $firstReply,
            'parent_id' => $this->parentComment->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $secondReply,
            'parent_id' => $this->parentComment->id,
        ]);

        $repliesCount = Comment::where('parent_id', $this->parentComment->id)->count();
        $this->assertEquals(2, $repliesCount);
    }
}
