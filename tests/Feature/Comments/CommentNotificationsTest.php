<?php

namespace Feature\Comments;

use App\Http\Livewire\AddComment;
use App\Http\Livewire\CommentNotifications;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use Tests\TestCase;

class CommentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    /* @test */
    public function test_comment_notifications_livewire_component_renders_when_user_logged_in()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('idea.index'));

        $response->assertSeeLivewire('comment-notifications');
    }

    /* @test */
    public function test_comment_notifications_livewire_component_does_not_renders_when_user_not_logged_ing()
    {
        $response = $this->get(route('idea.index'));

        $response->assertDontSeeLivewire('comment-notifications');
    }

    /* @test */
    public function test_notification_count_greater_than_threshold_shows_for_logged_user()
    {
        $user = User::factory()->create();

        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userCommenting = User::factory()->create();
        $threshold = CommentNotifications::NOTIFICATION_THRESHOLD;

        for ($i = 0; $i < $threshold + 1; $i++) {
            Livewire::actingAs($userCommenting)
                ->test(AddComment::class, ['idea' => $idea])
                ->set('comment', 'This first comment')
                ->call('addComment');
        }

        Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->assertSet('notificationCount', $threshold.'+')
            ->assertSee($threshold.'+');
    }

    /* @test */
    public function test_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userAComment = User::factory()->create();
        $userBComment = User::factory()->create();

        Livewire::actingAs($userAComment)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This first comment')
            ->call('addComment');

        Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAllAsRead');

        $this->assertEquals(0, $user->fresh()->unreadNotifications->count());
    }

    /* @test */
    public function test_can_mark_individual_notification_as_read()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userACommenting = User::factory()->create();
        $userBCommenting = User::factory()->create();

        Livewire::actingAs($userACommenting)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This is the first comment')
            ->call('addComment');

        Livewire::actingAs($userBCommenting)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This is the second comment')
            ->call('addComment');


        Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications');

        $firstNotification = DatabaseNotification::first();

        $this->assertNotNull($firstNotification);

        $component = Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAsRead', $firstNotification->id);

        $component->assertRedirect();

        $this->assertEquals(1, $user->fresh()->unreadNotifications->count());
    }

    /* @test */
    public function test_notification_idea_deleted_redirects_to_index_page()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userCommenting = User::factory()->create();

        Livewire::actingAs($userCommenting)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This is the first comment')
            ->call('addComment');

        $idea->comments()->delete();
        $idea->delete();

        Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAsRead', DatabaseNotification::first()->id)
            ->assertRedirect(route('idea.index'));
    }

    /* @test */
    public function test_notification_comment_deleted_redirects_to_index_page()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userCommenting = User::factory()->create();

        Livewire::actingAs($userCommenting)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This is the first comment')
            ->call('addComment');

        $idea->comments()->delete();

        Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAsRead', DatabaseNotification::first()->id)
            ->assertRedirect(route('idea.index'));
    }


    public function test_debug_individual_notification()
    {
        $user = User::factory()->create();
        $idea = Idea::factory()->create([
            'user_id' => $user->id,
        ]);

        $userCommenting = User::factory()->create();

        Livewire::actingAs($userCommenting)
            ->test(AddComment::class, ['idea' => $idea])
            ->set('comment', 'This is a test comment')
            ->call('addComment');


        $notifications = $user->fresh()->unreadNotifications;
        $this->assertEquals(1, $notifications->count());

        $firstNotification = $notifications->first();
        echo "Notification ID: " . $firstNotification->id . "\n";
        echo "Notification Data: " . json_encode($firstNotification->data) . "\n";
        echo "Idea ID: " . $idea->id . "\n";
        echo "Idea Slug: " . $idea->slug . "\n";
        echo "Expected Route: " . route('idea.show', ['idea' => $idea, 'page' => 1]) . "\n";

        $component = Livewire::actingAs($user)
            ->test(CommentNotifications::class)
            ->call('getNotifications')
            ->call('markAsRead', $firstNotification->id);

        $payload = $component->payload;
        if (isset($payload['effects']['redirect'])) {
            echo "Actual Redirect URL: " . $payload['effects']['redirect'] . "\n";
        }

        $this->assertTrue(true);
    }
}
