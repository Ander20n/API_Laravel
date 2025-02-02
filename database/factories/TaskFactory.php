<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'dueData' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'project_id' => function () {
                return \App\Models\Project::factory()->create()->id;
            },
        ];
    }
}
