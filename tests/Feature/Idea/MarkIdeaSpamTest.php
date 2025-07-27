<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Http\Livewire\MarkIdeaSpam;
use App\Http\Livewire\MarkIdeaNotSpam;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MarkIdeaSpamTest extends TestCase
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
        $this->idea = Idea::factory()->create([
            'user_id' => $this->ideaAuthor->id,
            'spam_reports' => 0
        ]);
    }

    // MarkIdeaSpam Tests

    /** @test */
    public function test_mark_idea_spam_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->assertStatus(200)
            ->assertViewIs('livewire.mark-idea-spam');
    }

    /** @test */
    public function test_guest_user_cannot_mark_idea_as_spam()
    {
        Livewire::test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals(0, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_authenticated_user_can_mark_idea_as_spam()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam')
            ->assertEmitted('ideaWasMarkedAsSpam', 'Idea was marked as spam!');

        $this->assertEquals(1, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_user_cannot_mark_own_idea_as_spam()
    {
        Livewire::actingAs($this->ideaAuthor)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam')
            ->assertEmitted('spamActionFailed', 'You cannot mark your own idea as spam.');

        $this->assertEquals(0, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_user_cannot_mark_same_idea_as_spam_twice()
    {
        // First mark as spam
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam')
            ->assertEmitted('ideaWasMarkedAsSpam', 'Idea was marked as spam!');

        // Try to mark again
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam')
            ->assertEmitted('spamActionFailed', 'You have already marked this idea as spam.');

        $this->assertEquals(1, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_spam_reports_counter_increments_correctly()
    {
        $this->idea->update(['spam_reports' => 3]);

        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        $this->assertEquals(4, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_session_stores_reported_idea_id()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        $spamReports = session()->get('spam_reports', []);
        $this->assertContains($this->idea->id, $spamReports);
    }

    /** @test */
    public function test_multiple_users_can_mark_same_idea_as_spam()
    {
        $anotherUser = User::factory()->create();

        // First user marks as spam
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        session()->flush();

        // Second user marks as spam
        Livewire::actingAs($anotherUser)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        $this->assertEquals(2, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_session_spam_reports_array_grows_correctly()
    {
        $anotherIdea = Idea::factory()->create(['user_id' => $this->ideaAuthor->id]);

        // Mark first idea as spam
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        // Mark second idea as spam
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $anotherIdea])
            ->call('markAsSpam');

        $spamReports = session()->get('spam_reports', []);
        $this->assertCount(2, $spamReports);
        $this->assertContains($this->idea->id, $spamReports);
        $this->assertContains($anotherIdea->id, $spamReports);
    }

    // MarkIdeaNotSpam Tests

    /** @test */
    public function test_mark_idea_not_spam_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaNotSpam::class, ['idea' => $this->idea])
            ->assertStatus(200)
            ->assertViewIs('livewire.mark-idea-not-spam');
    }

    /** @test */
    public function test_mark_idea_not_spam_component_mounts_with_correct_idea()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaNotSpam::class, ['idea' => $this->idea])
            ->assertSet('idea.id', $this->idea->id);
    }

    /** @test */
    public function test_guest_user_cannot_mark_idea_as_not_spam()
    {
        $this->idea->update(['spam_reports' => 5]);

        Livewire::test(MarkIdeaNotSpam::class, ['idea' => $this->idea])
            ->call('markAsNotSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals(5, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_regular_user_cannot_mark_idea_as_not_spam()
    {
        $this->idea->update(['spam_reports' => 5]);

        Livewire::actingAs($this->user)
            ->test(MarkIdeaNotSpam::class, ['idea' => $this->idea])
            ->call('markAsNotSpam')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertEquals(5, $this->idea->fresh()->spam_reports);
    }

    /** @test */
    public function test_idea_spam_reports_persists_in_database()
    {
        Livewire::actingAs($this->user)
            ->test(MarkIdeaSpam::class, ['idea' => $this->idea])
            ->call('markAsSpam');

        $this->assertDatabaseHas('ideas', [
            'id' => $this->idea->id,
            'spam_reports' => 1
        ]);
    }

    /** @test */
    public function test_spam_counter_reset_persists_in_database()
    {
        $this->idea->update(['spam_reports' => 10]);

        $this->assertDatabaseHas('ideas', [
            'id' => $this->idea->id,
            'spam_reports' => 10
        ]);
    }
}
