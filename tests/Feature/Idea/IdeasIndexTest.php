<?php

declare(strict_types=1);

namespace Tests\Feature\Idea;

use App\Enums\IdeaStatus;
use App\Http\Livewire\IdeasIndex;
use App\Models\Category;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IdeasIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function test_ideas_index_component_renders()
    {
        Livewire::test(IdeasIndex::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.ideas-index');
    }

    /** @test */
    public function test_ideas_index_shows_ideas()
    {
        $idea1 = Idea::factory()->create(['title' => 'First Idea']);
        $idea2 = Idea::factory()->create(['title' => 'Second Idea']);

        Livewire::test(IdeasIndex::class)
            ->assertSee('First Idea')
            ->assertSee('Second Idea');
    }

    /** @test */
    public function test_ideas_index_pagination_works()
    {
        Idea::factory()->count(20)->create();

        Livewire::test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() <= 15; // Default pagination
            });
    }

    /** @test */
    public function test_search_property_can_be_set()
    {
        Livewire::test(IdeasIndex::class)
            ->set('search', 'Laravel')
            ->assertSet('search', 'Laravel');
    }

    /** @test */
    public function test_category_property_can_be_set()
    {
        Livewire::test(IdeasIndex::class)
            ->set('category', 'Technology')
            ->assertSet('category', 'Technology');
    }

    /** @test */
    public function test_status_property_can_be_set()
    {
        Livewire::test(IdeasIndex::class)
            ->set('status', 'Open')
            ->assertSet('status', 'Open');
    }

    /** @test */
    public function test_clear_filters_resets_all_filters()
    {
        Livewire::test(IdeasIndex::class)
            ->set('search', 'test')
            ->set('category', '1')
            ->set('filter', 'Top Voted')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('category', 'All Categories')
            ->assertSet('filter', null)
            ->assertSet('status', IdeaStatus::All->value);
    }

    /** @test */
    public function test_set_filter_updates_filter()
    {
        Livewire::test(IdeasIndex::class)
            ->call('setFilter', 'Top Voted')
            ->assertSet('filter', 'Top Voted');
    }

    /** @test */
    public function test_filter_property_can_be_set()
    {
        Livewire::test(IdeasIndex::class)
            ->set('filter', 'Top Voted')
            ->assertSet('filter', 'Top Voted');
    }

    /** @test */
    public function test_updating_category_resets_page()
    {
        Idea::factory()->count(20)->create();

        Livewire::test(IdeasIndex::class)
            ->set('page', 2)
            ->set('category', $this->category->id)
            ->assertSet('page', 1);
    }

    /** @test */
    public function test_updating_search_resets_page()
    {
        Idea::factory()->count(20)->create();

        Livewire::test(IdeasIndex::class)
            ->set('page', 2)
            ->set('search', 'test')
            ->assertSet('page', 1);
    }

    /** @test */
    public function test_query_string_updated_status_resets_page()
    {
        Idea::factory()->count(20)->create();

        Livewire::test(IdeasIndex::class)
            ->set('page', 2)
            ->call('queryStringUpdatedStatus', 'Considering')
            ->assertSet('status', 'Considering')
            ->assertSet('page', 1);
    }

    /** @test */
    public function test_handle_idea_was_created_resets_page()
    {
        Idea::factory()->count(20)->create();

        Livewire::test(IdeasIndex::class)
            ->set('page', 2)
            ->call('handleIdeaWasCreated', 'Idea was created!')
            ->assertSet('page', 1);
    }

    /** @test */
    public function test_mount_sets_default_status_to_all()
    {
        Livewire::test(IdeasIndex::class)
            ->assertSet('status', 'All');
    }

    /** @test */
    public function test_component_displays_categories()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        Livewire::test(IdeasIndex::class)
            ->assertViewHas('categories', function ($categories) use ($category) {
                return $categories->contains('name', 'Test Category');
            });
    }

    /** @test */
    public function test_my_ideas_filter_works_for_authenticated_user()
    {
        $userIdea = Idea::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Idea',
        ]);

        $otherIdea = Idea::factory()->create(['title' => 'Other Idea']);

        Livewire::actingAs($this->user)
            ->test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertSee('My Idea')
            ->assertDontSee('Other Idea');
    }

    /** @test */
    public function test_ideas_with_no_results_displays_empty_state()
    {
        Livewire::test(IdeasIndex::class)
            ->set('search', 'nonexistent idea')
            ->assertDontSeeHtml('<div class="idea-container');
    }

}