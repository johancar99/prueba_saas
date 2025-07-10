<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Login successful'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'token',
                        'token_type',
                        'expires_in',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ]
                ]);
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_cannot_login_with_nonexistent_user(): void
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_can_logout_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // Obtener token a través del login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login', $loginData);
        $loginResponse->assertStatus(200);
        
        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Logout successful'
                ]);
    }

    public function test_cannot_logout_without_token(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'No token provided'
                ]);
    }

    public function test_can_logout_all_sessions(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // Obtener token a través del login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login', $loginData);
        $loginResponse->assertStatus(200);
        
        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'All sessions logged out successfully'
                ]);
    }

    public function test_can_get_current_user(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        // Obtener token a través del login
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login', $loginData);
        $loginResponse->assertStatus(200);
        
        $token = $loginResponse->json('data.token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
    }

    public function test_cannot_get_current_user_without_token(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthorized'
                ]);
    }

    public function test_login_removes_previous_tokens(): void
    {
        // Crear un usuario
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Primer login
        $firstLoginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $firstLoginResponse->assertStatus(200);
        $firstToken = $firstLoginResponse->json('data.access_token');

        // Verificar que el primer token funciona
        $meResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $firstToken
        ])->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200);

        // Segundo login (debería invalidar el primer token)
        $secondLoginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $secondLoginResponse->assertStatus(200);
        $secondToken = $secondLoginResponse->json('data.access_token');

        // Verificar que el segundo token funciona
        $meResponse2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $secondToken
        ])->getJson('/api/v1/auth/me');

        $meResponse2->assertStatus(200);

        // Verificar que el primer token ya no funciona
        $meResponse3 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $firstToken
        ])->getJson('/api/v1/auth/me');

        $meResponse3->assertStatus(401);
    }
} 