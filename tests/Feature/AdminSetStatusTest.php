<?php

namespace Tests\Feature;

use App\Http\Livewire\SetStatus;
use App\Jobs\NotifyAllVotes;
use Illuminate\Support\Facades\Queue;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSetStatusTest extends TestCase
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

    /* @test */
    public function test_show_page_contains_set_status_livewire_component_when_user_is_admin()
    {
        $user = User::factory()->admin()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('set-status');
    }

    /* @test */
    public function test_show_page_does_not_contain_set_status_livewire_component_when_user_is_not_admin()
    {
        $userNotAdmin = User::factory()->create();
        $idea = Idea::factory()->create();

        $this->actingAs($userNotAdmin)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('set-status');
    }

    /* @test */
    public function test_initial_status_set_correctly()
    {
        $user = User::factory()->create();

        $statusConsidering = Status::factory()->create([
            'id' => 2,
            'name' => 'Considering'
        ]);

        $idea = Idea::factory()->create([
            'status_id' => $statusConsidering
        ]);

        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea
            ])->assertSet('status', $statusConsidering->id);
    }

    public function test_can_set_status_correctly()
    {
        $user = User::factory()->admin()->create();

        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        $statusInProgress = Status::factory()->create(['id' => 3, 'name' => 'In Progress']);

        $idea = Idea::factory()->create([
            'status_id' => $statusConsidering->id,
        ]);

        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->set('status', $statusInProgress->id)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdating');

        $this->assertDatabaseHas('ideas', [
            'id' => $idea->id,
            'status_id' => $statusInProgress->id,
        ]);
    }

    public function test_can_set_status_correctly_while_notifying_all_voters()
    {
        $user = User::factory()->admin()->create();

        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        $statusInProgress = Status::factory()->create(['id' => 3, 'name' => 'In Progress']);

        $idea = Idea::factory()->create([
            'status_id' => $statusConsidering->id,
        ]);

        Queue::fake();
        Queue::assertNothingPushed();

        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea
            ])
            ->set('status', $statusInProgress->id)
            ->set('notifyAllVoters', true)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdating');

        Queue::assertPushed(NotifyAllVotes::class);
    }

}
