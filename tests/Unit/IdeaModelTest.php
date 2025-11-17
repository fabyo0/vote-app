<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private Status $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->status = Status::factory()->create();
    }

    /** @test */
    public function test_idea_belongs_to_user()
    {
        $idea = Idea::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(User::class, $idea->user);
        $this->assertEquals($this->user->id, $idea->user->id);
    }

    /** @test */
    public function test_idea_belongs_to_category()
    {
        $idea = Idea::factory()->create(['category_id' => $this->category->id]);

        $this->assertInstanceOf(Category::class, $idea->category);
        $this->assertEquals($this->category->id, $idea->category->id);
    }

    /** @test */
    public function test_idea_belongs_to_status()
    {
        $idea = Idea::factory()->create(['status_id' => $this->status->id]);

        $this->assertInstanceOf(Status::class, $idea->status);
        $this->assertEquals($this->status->id, $idea->status->id);
    }

    /** @test */
    public function test_idea_has_many_comments()
    {
        $idea = Idea::factory()->create();
        $comment1 = Comment::factory()->create(['idea_id' => $idea->id]);
        $comment2 = Comment::factory()->create(['idea_id' => $idea->id]);

        $this->assertCount(2, $idea->comments);
        $this->assertInstanceOf(Comment::class, $idea->comments->first());
    }

    /** @test */
    public function test_idea_has_many_votes()
    {
        $idea = Idea::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $idea->votes()->attach($user1->id);
        $idea->votes()->attach($user2->id);

        $this->assertCount(2, $idea->votes);
        $this->assertInstanceOf(User::class, $idea->votes->first());
    }

    /** @test */
    public function test_idea_fillable_attributes()
    {
        $idea = new Idea();
        $idea->fill([
            'user_id' => 1,
            'category_id' => 1,
            'status_id' => 1,
            'title' => 'Test Idea',
            'slug' => 'test-idea',
            'description' => 'Test description',
            'spam_reports' => 0,
        ]);

        $this->assertEquals(1, $idea->user_id);
        $this->assertEquals(1, $idea->category_id);
        $this->assertEquals(1, $idea->status_id);
        $this->assertEquals('Test Idea', $idea->title);
        $this->assertEquals('test-idea', $idea->slug);
        $this->assertEquals('Test description', $idea->description);
        $this->assertEquals(0, $idea->spam_reports);
    }

    /** @test */
    public function test_idea_can_be_created()
    {
        $idea = Idea::factory()->create([
            'title' => 'New Test Idea',
            'description' => 'This is a test description',
        ]);

        $this->assertDatabaseHas('ideas', [
            'title' => 'New Test Idea',
            'description' => 'This is a test description',
        ]);
    }

    /** @test */
    public function test_idea_can_be_updated()
    {
        $idea = Idea::factory()->create(['title' => 'Original Title']);

        $idea->update(['title' => 'Updated Title']);

        $this->assertDatabaseHas('ideas', [
            'id' => $idea->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function test_idea_can_be_deleted()
    {
        $idea = Idea::factory()->create();
        $ideaId = $idea->id;

        $idea->delete();

        $this->assertDatabaseMissing('ideas', [
            'id' => $ideaId,
        ]);
    }

    /** @test */
    public function test_idea_route_key_name_is_slug()
    {
        $idea = new Idea();

        $this->assertEquals('slug', $idea->getRouteKeyName());
    }

    /** @test */
    public function test_idea_has_default_per_page()
    {
        $idea = new Idea();

        $this->assertEquals(10, $idea->getPerPage());
    }

    /** @test */
    public function test_idea_slug_is_automatically_generated()
    {
        $idea = Idea::factory()->create(['title' => 'Test Idea Title']);

        $this->assertNotNull($idea->slug);
        $this->assertStringContainsString('test-idea-title', $idea->slug);
    }

    /** @test */
    public function test_idea_spam_reports_defaults_to_zero()
    {
        $idea = Idea::factory()->create(['spam_reports' => 0]);

        $this->assertEquals(0, $idea->spam_reports);
    }

    /** @test */
    public function test_idea_timestamps_are_set()
    {
        $idea = Idea::factory()->create();

        $this->assertNotNull($idea->created_at);
        $this->assertNotNull($idea->updated_at);
    }

    /** @test */
    public function test_idea_can_have_multiple_comments()
    {
        $idea = Idea::factory()->create();
        Comment::factory()->count(5)->create(['idea_id' => $idea->id]);

        $this->assertEquals(5, $idea->comments()->count());
    }

    /** @test */
    public function test_user_can_have_multiple_ideas()
    {
        $user = User::factory()->create();
        Idea::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertEquals(3, $user->ideas()->count());
    }

    /** @test */
    public function test_category_can_have_multiple_ideas()
    {
        $category = Category::factory()->create();
        Idea::factory()->count(4)->create(['category_id' => $category->id]);

        $this->assertEquals(4, $category->ideas()->count());
    }

    /** @test */
    public function test_status_can_have_multiple_ideas()
    {
        $status = Status::factory()->create();
        Idea::factory()->count(6)->create(['status_id' => $status->id]);

        $this->assertEquals(6, $status->ideas()->count());
    }
}