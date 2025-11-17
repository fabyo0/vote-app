<?php

declare(strict_types=1);

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCommentsTest extends TestCase
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
    public function test_idea_comments_livewire_component_renders()
    {
        Comment::factory()->create(['idea_id' => $this->idea->id]);

        $this->get(route('idea.show', $this->idea))
            ->assertSeeLivewire('idea-comments');
    }

    /** @test */
    public function test_idea_comment_livewire_component_renders()
    {
        Comment::factory()->create(['idea_id' => $this->idea->id]);

        $this->get(route('idea.show', $this->idea))
            ->assertSeeLivewire('idea-comment');
    }

    /** @test */
    public function test_no_comments_shows_appropriate_message()
    {
        $this->get(route('idea.show', $this->idea))
            ->assertSee('No comments yet...');
    }

    /** @test */
    public function test_single_comment_shows_on_idea_page()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is a single comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('This is a single comment')
            ->assertSee('1 Comment');
    }

    /** @test */
    public function test_list_comments_shows_on_idea_page()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is first comment',
        ]);

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is second comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSeeInOrder(['This is first comment', 'This is second comment'])
            ->assertSee('2 Comments');
    }

    /** @test */
    public function test_comments_show_in_correct_order()
    {
        $firstComment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'First comment by time',
            'created_at' => now()->subHours(2),
        ]);

        $secondComment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Second comment by time',
            'created_at' => now()->subHour(),
        ]);

        $thirdComment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Third comment by time',
            'created_at' => now(),
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee([
                'First comment by time',
                'Second comment by time',
                'Third comment by time'
            ]);
    }

    /** @test */
    public function test_list_comments_counts_shows_correctly_on_index_page()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is first comment',
        ]);

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is second comment',
        ]);

        $this->get(route('idea.index'))
            ->assertSee('2 Comments');
    }

    /** @test */
    public function test_comment_count_is_zero_when_no_comments()
    {
        $this->get(route('idea.index'))
            ->assertSee('0 Comments');
    }

    /** @test */
    public function test_comment_count_updates_correctly()
    {
        // Initially no comments
        $this->get(route('idea.show', $this->idea))
            ->assertSee('No comments yet...');

        // Add one comment
        Comment::factory()->create(['idea_id' => $this->idea->id]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('1 Comment');

        // Add second comment
        Comment::factory()->create(['idea_id' => $this->idea->id]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('2 Comments');
    }

    /** @test */
    public function test_op_badge_shows_if_author_idea_comments_on_idea()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'This is first comment',
        ]);

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'user_id' => $this->ideaAuthor->id,
            'body' => 'This is author comment',
        ]);

        $this->actingAs($this->user)
            ->get(route('idea.show', $this->idea))
            ->assertSee('OP');
    }

    /** @test */
    public function test_op_badge_does_not_show_for_non_author_comments()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'user_id' => $this->user->id,
            'body' => 'This is regular user comment',
        ]);

        $this->actingAs($this->user)
            ->get(route('idea.show', $this->idea))
            ->assertDontSeeLivewire('OP');
    }

    /** @test */
    public function test_comment_author_name_displays_correctly()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'user_id' => $this->user->id,
            'body' => 'Test comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee($this->user->name);
    }

    /** @test */
    public function test_comment_creation_date_displays()
    {
        $comment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Test comment with date',
            'created_at' => now()->subDays(2),
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('2 days ago');
    }

    /** @test */
    public function test_only_comments_for_specific_idea_show()
    {
        $anotherIdea = Idea::factory()->create();

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Comment for first idea',
        ]);

        Comment::factory()->create([
            'idea_id' => $anotherIdea->id,
            'body' => 'Comment for second idea',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('Comment for first idea')
            ->assertDontSee('Comment for second idea');
    }

    /** @test */
    public function test_comments_display_for_guest_users()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Comment visible to guests',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('Comment visible to guests');
    }

    /** @test */
    public function test_comments_display_for_authenticated_users()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Comment visible to authenticated users',
        ]);

        $this->actingAs($this->user)
            ->get(route('idea.show', $this->idea))
            ->assertSee('Comment visible to authenticated users');
    }

    /** @test */
    public function test_long_comment_displays_correctly()
    {
        $longComment = str_repeat('This is a very long comment. ', 20);

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => $longComment,
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee(substr($longComment, 0, 100)); // Test partial content
    }

    /** @test */
    public function test_multiple_ideas_comment_counts_show_correctly_on_index()
    {
        $secondIdea = Idea::factory()->create();

        // First idea - 2 comments
        Comment::factory()->count(2)->create(['idea_id' => $this->idea->id]);

        // Second idea - 3 comments
        Comment::factory()->count(3)->create(['idea_id' => $secondIdea->id]);

        $response = $this->get(route('idea.index'));

        $response->assertSee('2 Comments');
        $response->assertSee('3 Comments');
    }

    /** @test */
    public function test_comment_body_escapes_html_content()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => '<script>alert("xss")</script>Safe comment content',
        ]);

        $response = $this->get(route('idea.show', $this->idea));

        // Should see escaped content, not raw HTML
        $response->assertSee('Safe comment content');
        $response->assertDontSee('<script>alert("xss")</script>', false);
    }

    /** @test */
    public function test_comment_pagination_works_correctly()
    {
        // Create more comments than per page limit
        $comments = Comment::factory()->count(25)->create([
            'idea_id' => $this->idea->id,
        ]);

        $firstComment = $comments->first();
        $lastComment = $comments->last();

        // First page should show first comment but not last
        $response = $this->get(route('idea.show', $this->idea));
        $response->assertSee($firstComment->body);
        $response->assertDontSee($lastComment->body);

        // Check pagination links exist
        $response->assertSee('Next');
    }

    /** @test */
    public function test_no_comments_message_not_shown_when_comments_exist()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Existing comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee('Existing comment')
            ->assertDontSee('No comments yet...');
    }

    /** @test */
    public function test_comment_user_avatar_displays()
    {
        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'user_id' => $this->user->id,
            'body' => 'Test comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSee($this->user->getAvatar()); // Assuming getAvatar() method exists
    }

    /** @test */
    public function test_reply_comments_display_correctly()
    {
        $parentComment = Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'body' => 'Parent comment',
        ]);

        Comment::factory()->create([
            'idea_id' => $this->idea->id,
            'parent_id' => $parentComment->id,
            'body' => 'Reply comment',
        ]);

        $this->get(route('idea.show', $this->idea))
            ->assertSeeInOrder(['Parent comment', 'Reply comment']);
    }
}
