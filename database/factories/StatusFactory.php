<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{

    protected $model = Status::class;

    //protected $count = 5;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $statusOptions = [
            ['name' => 'Open', 'classes' => 'bg-gray-200'],
            ['name' => 'Considering', 'classes' => 'bg-purple text-white'],
            ['name' => 'In Progress', 'classes' => 'bg-yellow text-white'],
            ['name' => 'Implemented', 'classes' => 'bg-green text-white'],
            ['name' => 'Closed', 'classes' => 'bg-red text-white']
        ];

        $randomStatus = $this->faker->randomElement($statusOptions);

        return [
            'name' => $randomStatus['name'],
            'classes' => $randomStatus['classes']
        ];

    }
}
