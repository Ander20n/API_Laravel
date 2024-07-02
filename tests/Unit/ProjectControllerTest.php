<?php

namespace Tests\Unit;

use App\Http\Controllers\ProjectController;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Laravel\Passport\Passport;
use database\factories\UserFactory;
use database\factories\TaskFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
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

        $projects = Project::factory(3)->create();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                
            ]);
    }


    public function testStoreValidData()
    {

        $user = User::factory()->create();
        Passport::actingAs($user);

        $data = [
            'name' => 'New Project',
            'description' => 'This is a new project.',
            'startDate' => '2024-07-01',
            'endDate' => '2024-07-31',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseHas('projects', $data);
    }

    public function testStoreInvalidData()
    {
        $data = []; // Empty data

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(401)
        ->assertJsonStructure([
            'message',
        ]);
    }

    public function testShowExistingProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');
        
        $project = Project::factory()->create();
    
        $response = $this->get('/api/projects/' . $project->id);
    
        $response->assertStatus(200)
            ->assertJson($project->toArray());
    }    

    public function testShowNonexistentProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');
        
        $invalidId = 123; // Non-existent project ID

        $response = $this->get('/api/projects/' . $invalidId);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'error',
            ]);
    }

    public function testUpdateValidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $project = Project::factory()->create();

        $updatedData = [
            'name' => 'Updated Project Name',
            'description' => 'This is an updated project.',
            'startDate' => '2024-07-02',
            'endDate' => '2024-08-01',
        ];

        $response = $this->putJson('/api/projects/' . $project->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                 ]);

        $this->assertDatabaseHas('projects', $updatedData);
    }

    public function testUpdateInvalidData()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $project = Project::factory()->create();

        $invalidData = []; // Empty data

        $response = $this->putJson('/api/projects/' . $project->id, $invalidData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                    'errors',
                    'message',
                 ]);
    }

    public function testDestroyExistingProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $project = Project::factory()->create();

        $response = $this->delete('/api/projects/' . $project->id);

        $response->assertStatus(200)->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseMissing('projects', $project->toArray());
    }

    public function testDestroyNonexistentProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $invalidId = 123; // Non-existent project ID

        $response = $this->delete('/api/projects/' . $invalidId);

        $response->assertStatus(404)
        ->assertJsonStructure([
            'error',
            'message',
        ]);
    }

    public function testGetTasksExistingProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');
    
        $project = Project::factory()->create();
        $tasks = $project->tasks()->saveMany(Task::factory(3)->make());
    
        $response = $this->get('/api/projects/' . $project->id . '/tasks');
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'tasks',
                    'message',
                 ]);
    
        foreach ($tasks as $task) {
            $response->assertJsonFragment($task->toArray());
        }
    }    

    public function testGetTasksNonexistentProject()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->accessToken,
            'Accept' => 'application/json',
        ])->get('/api/projects');

        $invalidId = 123; // Non-existent project ID

        $response = $this->get('/api/projects/' . $invalidId . '/tasks');

        $response->assertStatus(404)
                 ->assertJsonStructure([
                    'error',
                    'message',
                 ]);
    }

}