<?php

namespace Feature\Filter;

use App\Http\Livewire\IdeasIndex;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;
use Tests\TestCase;

class OtherFiltersTest extends TestCase
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

    public function test_top_voted_filter_works()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOne = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOne->id,
            'description' => 'Description for my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $userB->id,
            'category_id' => $categoryTwo->id,
            'status_id' => $statusOne->id,
            'description' => 'Description for my second idea',
        ]);

        Vote::create([
            'idea_id' => $ideaOne->id,
            'user_id' => $user->id,
        ]);

        Vote::create([
            'idea_id' => $ideaOne->id,
            'user_id' => $userB->id,
        ]);

        Vote::create([
            'idea_id' => $ideaTwo->id,
            'user_id' => $userC->id,
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'Top Voted')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->count() === 2
                    && $ideas->get(1)->votes()->count() === 1;
            });
    }

    public function test_my_ideas_filter_works_correctly_when_user_is_not_logged_in()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $userB->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Third Idea',
            'description' => 'Description for my first idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'My Ideas')
            ->assertRedirect(route('login'));

    }

    public function test_my_ideas_filter_works_correctly_with_categories_filter()
    {
        $user = User::factory()->create();
        $userB = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryTwo->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Third Idea',
            'description' => 'Description for my first idea',
        ]);

        Livewire::actingAs($user)
            ->test(IdeasIndex::class)
            ->set('category', 'Category 1')
            ->set('filter', 'My Ideas')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 2
                    && $ideas->first()->title === 'My First Idea';
                //  && $ideas->get(1)->title == 'My First Idea';
            });
    }

    public function test_no_filters_works_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusOpen = Status::factory()->create(['name' => 'Open']);

        $ideaOne = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaTwo = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Second Idea',
            'description' => 'Description for my first idea',
        ]);

        $ideaThree = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusOpen->id,
            'title' => 'My Third Idea',
            'description' => 'Description for my first idea',
        ]);

        Livewire::test(IdeasIndex::class)
            ->set('filter', 'No filter')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() === 3
                    && $ideas->first()->title === 'My First Idea'
                    && $ideas->get(1)->title === 'My Second Idea';
            });
    }

    public function test_spam_comments_filter_test_works()
    {
        $user = User::factory()->admin()->create();

        $ideaOne = Idea::factory()->create([
            'title' => 'Idea One',
        ]);

        $ideaTwo = Idea::factory()->create([
            'title' => 'Idea Two',
        ]);

        $ideaThree = Idea::factory()->create([
            'title' => 'Idea Two',
        ]);

        $commentOne = Comment::factory()->create([
            'idea_id' => $ideaOne->id,
            'body' => 'This is my first comment',
            'spam_reports' => 3,
        ]);

        $commentTwo = Comment::factory()->create([
            'idea_id' => $ideaTwo->id,
            'body' => 'This is my second comment',
            'spam_reports' => 2,
        ]);

        Livewire::actingAs($user)
            ->test(IdeasIndex::class)
            ->set('filter', 'Spam Comments')
            ->assertViewHas('ideas', function ($ideas) {
                return $ideas->count() == 2;
            });
    }
}
