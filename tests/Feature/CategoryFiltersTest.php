<?php

namespace Tests\Feature;

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
            'name' => 'Category 1',
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
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Category 1')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 1';
            });
    }

    public function test_the_category_query_string_filters_correctly()
    {

        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 1',
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
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::withQueryParams(['category' => 'Category 1'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 1';
            });
    }

    public function test_selecting_a_category_filters_and_status_correctly()
    {

        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);

        $categoryTwo = Category::factory()->create([
            'name' => 'Category 1',
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
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'Category 1')
            ->set('status', 'Open')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 1'
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
            'name' => 'Category 1',
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
            'title' => 'My two title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::withQueryParams(['status' => 'Open', 'category' => 'Category 1'])
            ->test(IdeasIndex::class)
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->category->name === 'Category 1'
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
            'name' => 'Category 1',
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
            'category_id' => $categoryTwo->id,
            'title' => 'My Third title',
            'description' => 'Description for my two idea',
            'status_id' => $statusOpen->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('category', 'All Categories')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3;
            });
    }
}
