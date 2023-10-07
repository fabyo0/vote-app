<?php

namespace Tests\Unit;

use App\Http\Livewire\SetStatus;
use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_can_check_if_user_is_an_admin()
    {
        $user = User::factory()->create([
            'name' => 'Emre',
            'email' => 'emre@hotmail.com'
        ]);

        $userB = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@hotmail.com'
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($userB->isAdmin());
    }

}
