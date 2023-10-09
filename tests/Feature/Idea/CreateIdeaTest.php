<?php

namespace Feature\Idea;

use App\Http\Livewire\CreateIdea;
use App\Models\Category;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateIdeaTest extends TestCase
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

    public function test_create_idea_form_does_not_show_when_logged_out()
    {
        $response = $this->get(route('idea.index'));
        $response->assertSuccessful();
        $response->assertSee('Please login to create an idea.');
        $response->assertDontSee("Let us know what you would like and we'll take a look over!");
    }

    public function test_create_idea_form_does_show_when_logged()
    {
        $response = $this->actingAs(User::factory()->create())->get(route('idea.index'));

        $response->assertSuccessful();
        $response->assertDontSee('Please login to create an idea.');
        $response->assertSee('Let us know what you would like and we\'ll take a look over!', false);
    }

    public function test_main_page_contains_create_idea_livewire_component()
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('idea.index'))
            ->assertSeeLivewire('create-idea');

    }

    public function test_create_idea_form_validate_works()
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CreateIdea::class)
            ->set('title', '')
            ->set('category', '')
            ->set('description', '')
            ->call('createIdea')
            ->assertHasErrors(['title', 'description', 'category'])
            ->assertSee('The title field is required.');
    }

    public function test_creating_an_idea_works_correctly()
    {
        $user = User::factory()->create();

        $categoryOne = Category::factory()->create([
            'name' => 'Category 1',
        ]);
        $categoryTwo = Category::factory()->create([
            'name' => 'Category 2',
        ]);
        $statusOpen = Status::factory()->create([
            'name' => 'Open',
        ]);

        Livewire::actingAs($user)
            ->test(CreateIdea::class)
            ->set('title', 'My first ideas')
            ->set('category', $categoryOne->id)
            ->set('description', 'Lorem ipsum dolor sit amet...')
            ->call('createIdea')
            ->assertRedirect(route('idea.index'));

        $response = $this->actingAs($user)->get(route('idea.index'));
        $response->assertStatus(200);
        $response->assertSee('My first ideas');
        $response->assertSee('Lorem ipsum dolor sit amet...');

        // Check ideas table
        $this->assertDatabaseHas('ideas', [
            'title' => 'My first ideas',
            'description' => 'Lorem ipsum dolor sit amet...',
        ]);
    }
}
