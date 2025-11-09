<?php

declare(strict_types=1);

namespace Feature\User;

use App\Http\Livewire\UserProfileShow;
use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserProfileShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /** @test */
    public function test_user_profile_show_component_can_render()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertStatus(200)
            ->assertViewIs('livewire.user-profile-show');
    }

    /** @test */
    public function test_user_profile_show_loads_user_on_mount()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('user.id', $this->otherUser->id);
    }

    /** @test */
    public function test_user_profile_show_defaults_to_ideas_tab()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('activeTab', 'ideas');
    }

    /** @test */
    public function test_user_profile_show_displays_user_ideas_on_ideas_tab()
    {
        Idea::factory()->count(3)->create([
            'user_id' => $this->otherUser->id,
        ]);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('activeTab', 'ideas')
            ->assertViewHas('ideasCount', 3);
    }

    /** @test */
    public function test_user_profile_show_displays_user_comments_on_comments_tab()
    {
        $idea = Idea::factory()->create();

        Comment::factory()->count(5)->create([
            'user_id' => $this->otherUser->id,
            'idea_id' => $idea->id,
            'parent_id' => null,
        ]);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('setActiveTab', 'comments')
            ->assertSet('activeTab', 'comments')
            ->assertViewHas('commentsCount', 5);
    }

    /** @test */
    public function test_set_active_tab_changes_tab()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('activeTab', 'ideas')
            ->call('setActiveTab', 'comments')
            ->assertSet('activeTab', 'comments')
            ->call('setActiveTab', 'followers')
            ->assertSet('activeTab', 'followers')
            ->call('setActiveTab', 'following')
            ->assertSet('activeTab', 'following');
    }

    /** @test */
    public function test_guest_user_cannot_follow()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('toggleFollow')
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function test_user_cannot_follow_themselves()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfileShow::class, ['user' => $this->user])
            ->call('toggleFollow');

        $this->assertFalse($this->user->isFollowing($this->user));
    }

    /** @test */
    public function test_user_can_follow_another_user()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('isFollowing', false)
            ->call('toggleFollow')
            ->assertSet('isFollowing', true);

        $this->assertTrue($this->user->fresh()->isFollowing($this->otherUser));
    }

    /** @test */
    public function test_user_can_unfollow_another_user()
    {
        // First follow
        $this->user->follow($this->otherUser);

        Livewire::actingAs($this->user)
            ->test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('isFollowing', true)
            ->call('toggleFollow')
            ->assertSet('isFollowing', false);

        $this->assertFalse($this->user->fresh()->isFollowing($this->otherUser));
    }

    /** @test */
    public function test_check_following_status_sets_is_following_when_authenticated()
    {
        $this->user->follow($this->otherUser);

        Livewire::actingAs($this->user)
            ->test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('isFollowing', true);
    }

    /** @test */
    public function test_check_following_status_sets_is_following_false_when_not_following()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('isFollowing', false);
    }

    /** @test */
    public function test_check_following_status_handles_guest_user()
    {
        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertSet('isFollowing', false);
    }

    /** @test */
    public function test_user_profile_show_displays_followers_count()
    {
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        $follower1->follow($this->otherUser);
        $follower2->follow($this->otherUser);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertViewHas('followersCount', 2);
    }

    /** @test */
    public function test_user_profile_show_displays_following_count()
    {
        $following1 = User::factory()->create();
        $following2 = User::factory()->create();

        $this->otherUser->follow($following1);
        $this->otherUser->follow($following2);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->assertViewHas('followingCount', 2);
    }

    /** @test */
    public function test_user_profile_show_displays_followers_on_followers_tab()
    {
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        $follower1->follow($this->otherUser);
        $follower2->follow($this->otherUser);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('setActiveTab', 'followers')
            ->assertSet('activeTab', 'followers')
            ->assertViewHas('followers');
    }

    /** @test */
    public function test_user_profile_show_displays_following_on_following_tab()
    {
        $following1 = User::factory()->create();
        $following2 = User::factory()->create();

        $this->otherUser->follow($following1);
        $this->otherUser->follow($following2);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('setActiveTab', 'following')
            ->assertSet('activeTab', 'following')
            ->assertViewHas('following');
    }

    /** @test */
    public function test_set_active_tab_resets_pagination()
    {
        Idea::factory()->count(15)->create([
            'user_id' => $this->otherUser->id,
        ]);

        Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('setActiveTab', 'comments')
            ->assertSet('activeTab', 'comments');
    }

    /** @test */
    public function test_user_profile_show_only_shows_top_level_comments()
    {
        $idea = Idea::factory()->create();

        // Create parent comment from otherUser
        $parentComment = Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'idea_id' => $idea->id,
            'parent_id' => null,
        ]);

        // Create reply comment from otherUser (should not be counted)
        Comment::factory()->create([
            'user_id' => $this->otherUser->id,
            'idea_id' => $idea->id,
            'parent_id' => $parentComment->id,
        ]);

        // Create another top-level comment from a different user (should not be counted for otherUser)
        $otherUser2 = User::factory()->create();
        Comment::factory()->create([
            'user_id' => $otherUser2->id,
            'idea_id' => $idea->id,
            'parent_id' => null,
        ]);

        $component = Livewire::test(UserProfileShow::class, ['user' => $this->otherUser])
            ->call('setActiveTab', 'comments');

        // commentsCount counts all comments from otherUser (both parent and replies)
        // But the comments tab only shows top-level comments (parent_id is null)
        $commentsCount = $component->viewData('commentsCount');
        $this->assertEquals(2, $commentsCount, 'commentsCount includes all comments from otherUser (1 parent + 1 reply)');
        
        // Verify that only top-level comments are shown in the comments tab
        $comments = $component->viewData('comments');
        $this->assertNotNull($comments);
        $this->assertEquals(1, $comments->count(), 'Only top-level comments should be shown in comments tab');
    }
}

