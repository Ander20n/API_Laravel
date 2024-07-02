<?php

namespace Tests\Unit;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use database\factories\UserFactory;
use database\factories\TaskFactory;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase; // Reset database before each test

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PassportSeeder']);
    }

    public function testIndex()
    {
      $user = User::factory()->create();
      Passport::actingAs($user);
    
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
        'Accept' => 'application/json',
      ])->get('/api/tasks');
    
      $response->assertStatus(200);
      $this->assertJson($response->getContent());
    
      // No need to decode JSON, response is already an array
      $responseData = $response->json();
    }    

    public function testStoreValidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $project = Project::factory()->create();

        $data = [
            'title' => 'New Task',
            'description' => 'This is a new task.',
            'dueData' => '2024-07-15',
            'status' => 'pending',
            'project_id' => $project->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseHas('tasks', $data);
    }

    public function testStoreInvalidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');
        $data = []; // Empty data

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(422)
                ->assertJsonStructure([
                'message',
        ]);
    }

    public function testShowExistingTask()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');
        
        $task = Task::factory()->create();

        $response = $this->get('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $this->assertJson($response->getContent());
    }

    public function testShowNonexistentTask()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $invalidId = 123; // Non-existent task ID

        $response = $this->get('/api/tasks/' . $invalidId);

        $response->assertStatus(404)
        ->assertJsonStructure([
            'message',
        ]);
    }

    public function testUpdateValidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'This is an updated task.',
            'dueData' => '2024-07-20',
            'status' => 'completed',
            'project_id' => $project->id,
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $updatedData);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseHas('tasks', $updatedData);
    }

    public function testUpdateInvalidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $task = Task::factory()->create();

        $invalidData = []; // Empty data

        $response = $this->putJson('/api/tasks/' . $task->id, $invalidData);

        $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
        ]);
    }

    public function testDestroyExistingTask()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $task = Task::factory()->create();

        $response = $this->delete('/api/tasks/' . $task->id);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }

    public function testDestroyNonexistentTask()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
      
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
          'Accept' => 'application/json',
        ])->get('/api/tasks');

        $invalidId = 123; // Non-existent task ID

        $response = $this->delete('/api/tasks/' . $invalidId);

        $response->assertStatus(404)
        ->assertJsonStructure([
            'message',
        ]);
    }

}