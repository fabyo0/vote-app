<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\DeleteIdea;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteIdeaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $anotherUser;
    private Idea $idea;
    private Idea $anotherUserIdea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->idea = Idea::factory()->create(['user_id' => $this->user->id]);
        $this->anotherUserIdea = Idea::factory()->create(['user_id' => $this->anotherUser->id]);
    }

    /** @test */
    public function test_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->assertStatus(200)
            ->assertViewIs('livewire.delete-idea');
    }

    /** @test */
    public function test_component_mounts_with_correct_idea()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->assertSet('idea.id', $this->idea->id)
            ->assertSet('idea.title', $this->idea->title);
    }

    /** @test */
    public function test_guest_user_cannot_delete_idea()
    {
        Livewire::test(DeleteIdea::class, ['idea' => $this->idea])
            ->call('deleteIdea')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('ideas', ['id' => $this->idea->id]);
    }

    /** @test */
    public function test_authenticated_user_can_delete_own_idea()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertDatabaseMissing('ideas', ['id' => $this->idea->id]);
    }

    /** @test */
    public function test_user_cannot_delete_another_users_idea()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->anotherUserIdea])
            ->call('deleteIdea')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('ideas', ['id' => $this->anotherUserIdea->id]);
    }

    /** @test */
    public function test_success_message_is_flashed_after_deletion()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->call('deleteIdea')
            ->assertRedirect(route('idea.index'));

        $this->assertEquals('Idea was deleted successfully!', session('success_message'));
    }

    /** @test */
    public function test_idea_deletion_removes_related_comments()
    {
        $comment = Comment::factory()->create(['idea_id' => $this->idea->id]);

        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->call('deleteIdea');

        $this->assertDatabaseMissing('ideas', ['id' => $this->idea->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function test_idea_with_many_comments_can_be_deleted()
    {
        $comments = Comment::factory()->count(10)->create(['idea_id' => $this->idea->id]);

        Livewire::actingAs($this->user)
            ->test(DeleteIdea::class, ['idea' => $this->idea])
            ->call('deleteIdea');

        $this->assertDatabaseMissing('ideas', ['id' => $this->idea->id]);

        foreach ($comments as $comment) {
            $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        }
    }
}
