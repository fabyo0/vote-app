<?php

namespace Feature\Filter;

use App\Http\Livewire\IdeasIndex;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;
use Tests\TestCase;

class CategoryFiltersTest extends TestCase
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

    public function test_selecting_a_category_filters_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);

        $categoryThree = Category::factory()->create([
            'name' => 'Category 3',
        ]);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'My first title',
            'description' => 'Description for my first idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My three title',
            'description' => 'Description for my three idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Category 2')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 2';
            });
    }

    public function test_the_category_query_string_filters_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);

        $categoryThree = Category::factory()->create([
            'name' => 'Category 3',
        ]);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'My first title',
            'description' => 'Description for my first idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My three title',
            'description' => 'Description for my three idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::withQueryParams(['category' => 'Category 2'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 2';
            });
    }

    public function test_selecting_a_category_filters_and_status_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);

        $categoryThree = Category::factory()->create([
            'name' => 'Category 3',
        ]);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'My first title',
            'description' => 'Description for my first idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My three title',
            'description' => 'Description for my three idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Category 2')
            ->set('status', 'Open')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 2'
                    && $ideas->first()->status->name === 'Open';
            });
    }

    public function test_selecting_a_category_filters_and_status_and_category_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);

        $categoryThree = Category::factory()->create([
            'name' => 'Category 3',
        ]);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'My first title',
            'description' => 'Description for my first idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My three title',
            'description' => 'Description for my three idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::withQueryParams(['status' => 'Open', 'category' => 'Category 2'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 2'
                    && $ideas->first()->status->name === 'Open';
            });
    }

    public function test_selecting_all_categories_filters_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);

        $categoryThree = Category::factory()->create([
            'name' => 'Category 3',
        ]);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'title' => 'My First title',
            'description' => 'Description for my first idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'title' => 'My Two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryThree->id,
            'title' => 'My Third title',
            'description' => 'Description for my third idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'All Categories')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3;
            });
    }


    public function test_filtering_by_category_with_no_ideas_returns_empty()
    {
        Category::factory()->create(['name' => 'Empty Category']);
        Livewire::test(IdeasIndex::class)
            ->set('category', 'Empty Category')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->isEmpty();
            });
    }

    public function test_invalid_category_name_returns_all_ideas()
    {
        $user = User::factory()->create();
        $status = Status::factory()->create(['name' => 'Open']);
        $category = Category::factory()->create(['name' => 'Valid Category']);

        Idea::factory(2)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status_id' => $status->id,
        ]);

        Livewire::withQueryParams(['category' => 'NonExistentCategory'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2;
            });
    }

    public function test_category_and_different_status_combination_filters_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Design']);
        $statusOpen = Status::factory()->create(['name' => 'Open']);
        $statusClosed = Status::factory()->create(['name' => 'Closed']);

        Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
        ]);

        Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusClosed->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Design')
            ->set('status', 'Closed')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 1
                    && $ideas->first()->category->name === 'Design'
                    && $ideas->first()->status->name === 'Closed';
            });
    }

}
