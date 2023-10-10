<?php

namespace Feature\Filter;

use App\Http\Livewire\IdeasIndex;
use App\Http\Livewire\StatusFilter;
use App\Models\Idea;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;
use Tests\TestCase;

class StatusFilterTest extends TestCase
{
    use RefreshDatabase;

    /* @test */

    public function test_index_page_contains_status_filters_livewire_component()
    {
        Idea::factory()->create();
        $this->get(route('idea.index'))
            ->assertSeeLivewire('status-filter');
    }

    public function test_show_page_contains_status_filters_livewire_component()
    {
        $idea = Idea::factory()->create();
        $this->get(route('idea.show', $idea))
            ->assertSeeLivewire('status-filter');

    }

    public function test_shows_correct_status_count()
    {
        $statusImplement = Status::factory()->create(['id' => 4, 'name' => 'Implemented']);

        Idea::factory()->create([
            'status_id' => $statusImplement->id
        ]);

        Idea::factory()->create([
            'status_id' => $statusImplement->id
        ]);

        Livewire::test(StatusFilter::class)
            ->assertSee('All Ideas (2)')
            ->assertSee('Implemented (2)');
    }

    public function test_filtering_works_when_query_string_in_place()
    {
        $statusOpen = Status::factory()->create(['name' => 'Open']);
        $statusConsidering = Status::factory()->create(['name' => 'Considering', 'classes' => 'bg-purple text-white']);
        $statusInProgress = Status::factory()->create(['name' => 'In Progress', 'classes' => 'bg-yellow text-white']);
        $statusImplemented = Status::factory()->create(['name' => 'Implemented']);
        $statusClosed = Status::factory()->create(['name' => 'Closed']);

        Idea::factory()->create([
            'status_id' => $statusConsidering->id,
        ]);

        Idea::factory()->create([
            'status_id' => $statusConsidering->id,
        ]);

        Idea::factory()->create([
            'status_id' => $statusInProgress->id,
        ]);

        Idea::factory()->create([
            'status_id' => $statusInProgress->id,
        ]);

        Idea::factory()->create([
            'status_id' => $statusInProgress->id,
        ]);

        Livewire::withQueryParams(['status' => 'In Progress'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3
                    && $ideas->first()->status->name === 'In Progress';
            });
    }

    public function test_show_page_does_not_show_selected_status()
    {
        $statusImplemented = Status::factory()->create(['name' => 'Implemented']);

        $idea = Idea::factory()->create([
            'status_id' => $statusImplemented->id
        ]);

        $response = $this->get(route('idea.show', $idea));

        $response->assertDontSee('border-blue text-gray-900');
    }


    public function test_index_page_does_not_show_selected_status()
    {
        $statusImplemented = Status::factory()->create(['name' => 'Implemented']);

        $idea = Idea::factory()->create([
            'status_id' => $statusImplemented->id
        ]);

        $response = $this->get(route('idea.show', $idea));

        $response->assertDontSee('border-blue text-gray-900');
    }

}
