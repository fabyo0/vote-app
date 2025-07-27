<?php

namespace Feature\Filter;

use App\Http\Livewire\IdeasIndex;
use App\Http\Livewire\StatusFilter;
use App\Models\Idea;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatusFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statuses = [
            'open' => Status::factory()->create(['name' => 'Open']),
            'considering' => Status::factory()->create(['name' => 'Considering']),
            'in_progress' => Status::factory()->create(['name' => 'In Progress']),
            'implemented' => Status::factory()->create(['name' => 'Implemented']),
            'closed' => Status::factory()->create(['name' => 'Closed']),
        ];
    }

    /** @test */
    public function index_page_contains_status_filters_livewire_component()
    {
        Idea::factory()->create(['status_id' => $this->statuses['open']->id]);

        $this->get(route('idea.index'))
            ->assertOk()
            ->assertSeeLivewire('status-filter');
    }

    /** @test */
    public function show_page_contains_status_filters_livewire_component()
    {
        $idea = Idea::factory()->create(['status_id' => $this->statuses['open']->id]);

        $this->get(route('idea.show', $idea))
            ->assertOk()
            ->assertSeeLivewire('status-filter');
    }

    /** @test */
    public function filtering_works_for_each_status_type()
    {
        Idea::factory(2)->create(['status_id' => $this->statuses['open']->id]);
        Idea::factory(3)->create(['status_id' => $this->statuses['considering']->id]);
        Idea::factory(1)->create(['status_id' => $this->statuses['implemented']->id]);

        foreach ($this->statuses as $status) {
            $count = Idea::where('status_id', $status->id)->count();

            Livewire::withQueryParams(['status' => $status->name])
                ->test(IdeasIndex::class)
                ->assertViewHas('ideas', function ($ideas) use ($status, $count) {
                    return $ideas->count() === $count
                        && ($count === 0 || $ideas->first()->status->name === $status->name);
                });
        }
    }

    /** @test */
    public function show_page_does_not_show_selected_status_for_any_status()
    {
        foreach ($this->statuses as $status) {
            $idea = Idea::factory()->create(['status_id' => $status->id]);

            $this->get(route('idea.show', $idea))
                ->assertOk()
                ->assertDontSee('border-blue text-gray-900');
        }
    }

    /** @test */
    public function status_filter_handles_non_existent_status_gracefully()
    {
        Livewire::withQueryParams(['status' => 'NonExistentStatus'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === Idea::count();
            });
    }

    /** @test */
    public function index_page_shows_all_ideas_by_default()
    {
        Idea::factory(5)->create();

        Livewire::test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 5;
            });
    }

    /** @test */
    public function filtering_by_status_with_no_ideas_shows_empty_result()
    {
        $emptyStatus = Status::factory()->create(['name' => 'Empty Status']);

        Livewire::withQueryParams(['status' => 'Empty Status'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->isEmpty();
            });
    }

    /** @test */
    public function status_filter_can_switch_between_statuses()
    {
        Idea::factory(1)->create(['status_id' => $this->statuses['open']->id]);
        Idea::factory(2)->create(['status_id' => $this->statuses['closed']->id]);

        Livewire::withQueryParams(['status' => 'Open'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 1 && $ideas->first()->status->name === 'Open';
            })
            ->set('status', 'Closed')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2 && $ideas->first()->status->name === 'Closed';
            });
    }
}
