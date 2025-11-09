<?php

declare(strict_types=1);

namespace Feature\User;

use App\Http\Livewire\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);
    }

    /** @test */
    public function test_user_profile_component_can_render()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.user-profile');
    }

    /** @test */
    public function test_user_profile_loads_user_data_on_mount()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->assertSet('name', 'John Doe')
            ->assertSet('email', 'john@example.com');
    }


    /** @test */
    public function test_update_profile_validates_name_required()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', '')
            ->set('email', 'john@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function test_update_profile_validates_name_min_length()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'Jo')
            ->set('email', 'john@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function test_update_profile_validates_email_required()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'John Doe')
            ->set('email', '')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function test_update_profile_validates_email_format()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'John Doe')
            ->set('email', 'invalid-email')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function test_update_profile_validates_email_unique()
    {
        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
        ]);

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'John Doe')
            ->set('email', 'other@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function test_update_profile_allows_same_email()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'John Updated')
            ->set('email', 'john@example.com')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertEquals('John Updated', $this->user->fresh()->name);
        $this->assertEquals('john@example.com', $this->user->fresh()->email);
    }

    /** @test */
    public function test_update_profile_updates_name_and_email()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->call('updateProfile')
            ->assertHasNoErrors();

        $this->assertEquals('Jane Doe', $this->user->fresh()->name);
        $this->assertEquals('jane@example.com', $this->user->fresh()->email);
    }

    /** @test */
    public function test_update_profile_validates_avatar_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('avatar', $file)
            ->call('updateProfile')
            ->assertHasErrors(['avatar']);
    }

    /** @test */
    public function test_update_profile_validates_avatar_max_size()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg')->size(3000); // 3MB

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('avatar', $file)
            ->call('updateProfile')
            ->assertHasErrors(['avatar']);
    }

    /** @test */
    public function test_update_password_validates_current_password_required()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', '')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'newpassword123')
            ->call('updatePassword')
            ->assertHasErrors(['currentPassword']);
    }

    /** @test */
    public function test_update_password_validates_new_password_required()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', 'password123')
            ->set('newPassword', '')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword']);
    }

    /** @test */
    public function test_update_password_validates_new_password_min_length()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', 'password123')
            ->set('newPassword', 'short')
            ->set('newPasswordConfirmation', 'short')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword']);
    }

    /** @test */
    public function test_update_password_validates_new_password_confirmation()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', 'password123')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'differentpassword')
            ->call('updatePassword')
            ->assertHasErrors(['newPassword']);
    }

    /** @test */
    public function test_update_password_validates_current_password_correct()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', 'wrongpassword')
            ->set('newPassword', 'newpassword123')
            ->set('newPasswordConfirmation', 'newpassword123')
            ->call('updatePassword');

        // Password should not be updated when current password is wrong
        $this->assertTrue(Hash::check('password123', $this->user->fresh()->password));
        $this->assertFalse(Hash::check('newpassword123', $this->user->fresh()->password));
    }


    /** @test */
    public function test_toggle_password_form_shows_and_hides_form()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->assertSet('showPasswordForm', false)
            ->call('togglePasswordForm')
            ->assertSet('showPasswordForm', true)
            ->call('togglePasswordForm')
            ->assertSet('showPasswordForm', false);
    }

    /** @test */
    public function test_toggle_password_form_resets_password_fields()
    {
        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('currentPassword', 'test')
            ->set('newPassword', 'test')
            ->set('newPasswordConfirmation', 'test')
            ->call('togglePasswordForm')
            ->assertSet('currentPassword', null)
            ->assertSet('newPassword', null)
            ->assertSet('newPasswordConfirmation', null);
    }

    /** @test */
    public function test_remove_avatar_clears_avatar_and_temporary_avatar()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('avatar', $file)
            ->call('removeAvatar')
            ->assertSet('avatar', null)
            ->assertSet('temporaryAvatar', null);
    }

    /** @test */
    public function test_updated_avatar_validates_and_sets_temporary_avatar()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        Livewire::actingAs($this->user)
            ->test(UserProfile::class)
            ->set('avatar', $file)
            ->assertHasNoErrors()
            ->assertSet('temporaryAvatar.name', 'avatar.jpg');
    }
}

