<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'email' => 'emredikmen002@gmail.com',
        ]);

        $userB = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@hotmail.com',
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($userB->isAdmin());
    }
}
