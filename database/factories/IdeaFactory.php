<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //TODO: foreach user id
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'status_id' => $this->faker->numberBetween(1,5),
            'title' => ucwords($this->faker->words(4, true)),
            'description' => $this->faker->paragraph(5)
        ];
    }
}
