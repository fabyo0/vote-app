<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'idea_id' => Idea::factory(),
            'status_id' => Status::factory(),
            'body' => $this->faker->paragraph(5),
        ];
    }


    public function existing()
    {
        return $this->state(function () {
            return [
                'user_id' => $this->faker->numberBetween(1, 20),
                'status_id' => 1
            ];
        });
    }
}
