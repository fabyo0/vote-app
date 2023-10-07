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


    public function test_show_page_contains_set_status_livewire_component_when_user_is_admin()
    {
        $user = User::factory()->create([
            'email' => 'emre@hotmail.com'
        ]);

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
            'classes' => 'bg-gray-200',
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertSeeLivewire('set-status');
    }


    public function test_show_page_contains_set_status_livewire_component_when_user_is_not_admin()
    {
        $user = User::factory()->create([
            'email' => 'test@hotmail.com'
        ]);

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusOpen = Status::factory()->create([
            'name' => 'Open',
            'classes' => 'bg-gray-200',
        ]);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'status_id' => $statusOpen,
            'category_id' => $categoryOne,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        $this->actingAs($user)
            ->get(route('idea.show', $idea))
            ->assertDontSeeLivewire('set-status');
    }


    public function test_initial_status_is_set_correctly()
    {
        $user = User::factory()->create([
            'email' => 'emre@hotmail.com',
        ]);

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusConsidering->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea
            ])
            ->assertSet('status', $statusConsidering->id);

    }



    //FIXME: Failed asserting that a row in the table [ideas] matches the attributes {
    public function test_can_status_set_correctly()
    {
        $user = User::factory()->create([
            'email' => 'emre@hotmail.com',
        ]);

        $categoryOne = Category::factory()->create(['name' => 'Category 1']);
        $categoryTwo = Category::factory()->create(['name' => 'Category 2']);

        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        $statusInProgress = Status::factory()->create(['id' => 3, 'name' => 'In Progress']);

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusConsidering->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);


        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea
            ])
            ->set('status', $statusConsidering->id)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdating');

        $this->assertDatabaseHas('ideas', [
            'id' => $idea->id,
            'status_id' => $statusInProgress->id
        ]);

    }


    public function test_can_set_status_correctly_while_notifying_all_voters()
    {
        // Test için bir kullanıcı oluşturuluyor.
        $user = User::factory()->create([
            'email' => 'emredikmen002@gmail.com',
        ]);

        // Bir kategori oluşturuluyor ve ismi 'Category 1' olarak belirleniyor.
        $categoryOne = Category::factory()->create(['name' => 'Category 1']);

        // İki farklı durum (status) oluşturuluyor: 'Considering' ve 'In Progress'.
        $statusConsidering = Status::factory()->create(['id' => 2, 'name' => 'Considering']);
        $statusInProgress = Status::factory()->create(['id' => 3, 'name' => 'In Progress']);

        // Bir fikir (idea) oluşturuluyor ve bu fikir, yukarıda oluşturulan kullanıcı, kategori ve durum ile ilişkilendiriliyor.
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
            'category_id' => $categoryOne->id,
            'status_id' => $statusConsidering->id,
            'title' => 'My First Idea',
            'description' => 'Description for my first idea',
        ]);

        // Laravel'deki "Queue" sistemi sahte (fake) bir hale getiriliyor, yani gerçek bir işlem yapmadan sadece taklit ediliyor.
        Queue::fake();

        // Hiçbir şeyin kuyruğa eklenmediğini doğruluyoruz.
        Queue::assertNothingPushed();

        // Livewire test özelliği kullanılarak, belirli bir kullanıcı kimliği ile bir bileşeni (component) test ediyoruz.
        // Bu bileşen, "SetStatus" adını taşıyor ve "idea" özelliği ile oluşturulan fikri alır.
        // Daha sonra, bu bileşenin "status" ve "notifyAllVoters" özelliklerini belirli değerlere ayarlıyoruz.
        // Son olarak, "setStatus" işlevini çağırıyoruz ve "statusWasUpdating" adlı bir olayın yayınlandığını doğruluyoruz.
        \Livewire::actingAs($user)
            ->test(SetStatus::class, [
                'idea' => $idea,
            ])
            ->set('status', $statusInProgress->id)
            ->set('notifyAllVoters', true)
            ->call('setStatus')
            ->assertEmitted('statusWasUpdating');

        // Kuyruğa "NotifyAllVotes" sınıfının eklenip eklenmediğini doğruluyoruz.
        Queue::assertPushed(NotifyAllVotes::class);
    }

}
