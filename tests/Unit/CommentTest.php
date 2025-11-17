<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Idea $idea;
    private Status $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->status = Status::factory()->create();
        $this->idea = Idea::factory()->create();
    }

    /** @test */
    public function test_comment_belongs_to_user()
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $comment->user);
        $this->assertEquals($this->user->id, $comment->user->id);
    }

    /** @test */
    public function test_comment_belongs_to_idea()
    {
        $comment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
        ]);

        $this->assertInstanceOf(Idea::class, $comment->idea);
        $this->assertEquals($this->idea->id, $comment->idea->id);
    }

    /** @test */
    public function test_comment_belongs_to_status()
    {
        $comment = Comment::factory()->create([
            'status_id' => $this->status->id,
        ]);

        $this->assertInstanceOf(Status::class, $comment->status);
        $this->assertEquals($this->status->id, $comment->status->id);
    }

    /** @test */
    public function test_comment_can_have_parent()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create([
            'parent_id' => $parentComment->id,
        ]);

        $this->assertInstanceOf(Comment::class, $childComment->parent);
        $this->assertEquals($parentComment->id, $childComment->parent->id);
    }

    /** @test */
    public function test_comment_can_have_replies()
    {
        $parentComment = Comment::factory()->create();
        $reply1 = Comment::factory()->create(['parent_id' => $parentComment->id]);
        $reply2 = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertCount(2, $parentComment->replies);
        $this->assertTrue($parentComment->replies->contains($reply1));
        $this->assertTrue($parentComment->replies->contains($reply2));
    }

    /** @test */
    public function test_comment_without_parent_has_null_parent()
    {
        $comment = Comment::factory()->create(['parent_id' => null]);

        $this->assertNull($comment->parent_id);
        $this->assertNull($comment->parent);
    }

    /** @test */
    public function test_comment_is_status_update_casts_to_boolean()
    {
        $comment = Comment::factory()->create([
            'is_status_update' => true,
        ]);

        $this->assertIsBool($comment->is_status_update);
        $this->assertTrue($comment->is_status_update);
    }

    /** @test */
    public function test_comment_fillable_attributes()
    {
        $comment = new Comment();
        $comment->fill([
            'user_id' => 1,
            'idea_id' => 1,
            'parent_id' => null,
            'status_id' => 1,
            'body' => 'Test comment body',
            'is_status_update' => false,
        ]);

        $this->assertEquals(1, $comment->user_id);
        $this->assertEquals(1, $comment->idea_id);
        $this->assertEquals(1, $comment->status_id);
        $this->assertEquals('Test comment body', $comment->body);
        $this->assertFalse($comment->is_status_update);
    }

    /** @test */
    public function test_comment_can_be_created()
    {
        $comment = Comment::factory()->create([
            'body' => 'This is a test comment',
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
        ]);
    }

    /** @test */
    public function test_comment_can_be_updated()
    {
        $comment = Comment::factory()->create([
            'body' => 'Original comment',
        ]);

        $comment->update(['body' => 'Updated comment']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Updated comment',
        ]);
    }

    /** @test */
    public function test_comment_can_be_deleted()
    {
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $comment->delete();

        $this->assertDatabaseMissing('comments', [
            'id' => $commentId,
        ]);
    }

    /** @test */
    public function test_comment_has_default_per_page()
    {
        $comment = new Comment();

        $this->assertEquals(7, $comment->getPerPage());
    }

    /** @test */
    public function test_comment_spam_reports_default_value()
    {
        $comment = Comment::factory()->create();

        $this->assertEquals(0, $comment->spam_reports);
    }

    /** @test */
    public function test_parent_only_scope()
    {
        $parentComment1 = Comment::factory()->create(['parent_id' => null]);
        $parentComment2 = Comment::factory()->create(['parent_id' => null]);
        $replyComment = Comment::factory()->create(['parent_id' => $parentComment1->id]);

        $parentComments = Comment::parentOnly()->get();

        $this->assertCount(2, $parentComments);
        $this->assertTrue($parentComments->contains($parentComment1));
        $this->assertTrue($parentComments->contains($parentComment2));
        $this->assertFalse($parentComments->contains($replyComment));
    }

    /** @test */
    public function test_replies_only_scope()
    {
        $parentComment = Comment::factory()->create(['parent_id' => null]);
        $reply1 = Comment::factory()->create(['parent_id' => $parentComment->id]);
        $reply2 = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $replies = Comment::repliesOnly()->get();

        $this->assertCount(2, $replies);
        $this->assertTrue($replies->contains($reply1));
        $this->assertTrue($replies->contains($reply2));
        $this->assertFalse($replies->contains($parentComment));
    }

    /** @test */
    public function test_comment_timestamps_are_set()
    {
        $comment = Comment::factory()->create();

        $this->assertNotNull($comment->created_at);
        $this->assertNotNull($comment->updated_at);
    }
}