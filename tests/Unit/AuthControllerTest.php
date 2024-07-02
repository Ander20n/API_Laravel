<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // Reset database before each test

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PassportSeeder']);
    }

    public function testRegisterValidData()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret123',
            'c_password' => 'secret123',
        ];
    
        $response = $this->postJson('/api/register', $data);
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'token',
                         'name',
                     ],
                     'message',
                 ]);
    
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
    }    
    
    public function testRegisterInvalidData()
    {
        $data = [
            'name' => '', // Missing name
            'email' => 'invalid_email', // Invalid email format
            'password' => 'short', // Short password
            'c_password' => 'different_password', // Password confirmation mismatch
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(404)
                 ->assertJsonStructure([
                     'message',
                 ]);
    }

    public function testLoginValidCredentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'secret123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'token',
                         'name',
                     ],
                     'message',
                 ]);

        $token = $response->json('success.token');

        // Agora você pode usar o token para acessar rotas protegidas
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/protected-route')
          ->assertStatus(404);
    }

    public function testLoginInvalidCredentials()
    {
        $data = [
            'email' => 'nonexistent@example.com', // Non-existent email
            'password' => 'invalid_password',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(404)
                 ->assertJsonStructure([
                     'message',
                 ]);
    }
}
