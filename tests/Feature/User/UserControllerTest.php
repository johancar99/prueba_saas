<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar el seeder de roles y permisos
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RoleAndPermissionSeeder']);
        
        // Crear un usuario super-admin para autenticación con email único
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_' . uniqid() . '@example.com',
            'password' => bcrypt('password')
        ]);
        $this->superAdmin->assignRole('super-admin');

        // Crear una empresa para las pruebas
        $this->company = Company::factory()->create();
    }

    public function test_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin)
                        ->getJson('/api/v1/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'company_id',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to'
                    ]
                ]);
    }

    public function test_can_create_user(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'company_id' => $this->company->id
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'User created successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'company_id',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company_id' => $this->company->id
        ]);
    }

    public function test_cannot_create_user_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'company_id' => $this->company->id
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_user_when_company_user_limit_reached(): void
    {
        // Crear un plan con límite de 1 usuario
        $plan = \App\Models\Plan::create([
            'name' => 'Plan Limitado',
            'monthly_price' => 9.99,
            'user_limit' => 1,
            'features' => json_encode(['feature1'])
        ]);

        // Crear una empresa con el plan limitado
        $company = Company::factory()->create();
        
        // Crear suscripción activa para la empresa
        \App\Models\Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'is_active' => true,
            'starts_at' => now(),
            'ends_at' => now()->addYear()
        ]);

        // Crear un usuario (el primero, que está dentro del límite)
        $firstUser = User::factory()->create([
            'company_id' => $company->id
        ]);

        // Intentar crear un segundo usuario (debería fallar)
        $userData = [
            'name' => 'Segundo Usuario',
            'email' => 'segundo@example.com',
            'password' => 'password123',
            'company_id' => $company->id
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->postJson('/api/v1/users', $userData);

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'User limit for this company plan has been reached.'
                ]);
    }

    public function test_can_create_user_when_company_has_no_user_limit(): void
    {
        // Crear un plan sin límite de usuarios (user_limit = null o 0)
        $plan = \App\Models\Plan::create([
            'name' => 'Plan Sin Límite',
            'monthly_price' => 19.99,
            'user_limit' => null,
            'features' => json_encode(['feature1', 'feature2'])
        ]);

        // Crear una empresa con el plan sin límite
        $company = Company::factory()->create();
        
        // Crear suscripción activa para la empresa
        \App\Models\Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'is_active' => true,
            'starts_at' => now(),
            'ends_at' => now()->addYear()
        ]);

        // Crear múltiples usuarios (debería funcionar)
        for ($i = 1; $i <= 5; $i++) {
            $userData = [
                'name' => "Usuario {$i}",
                'email' => "usuario{$i}@example.com",
                'password' => 'password123',
                'company_id' => $company->id
            ];

            $response = $this->actingAs($this->superAdmin)
                            ->postJson('/api/v1/users', $userData);

            $response->assertStatus(201);
        }
    }

    public function test_can_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
                        ->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        $response = $this->actingAs($this->superAdmin)
                        ->getJson('/api/v1/users/999');

        $response->assertStatus(404);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->putJson("/api/v1/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User updated successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
                        ->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User deleted successfully'
                ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_can_restore_user(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $response = $this->actingAs($this->superAdmin)
                        ->patchJson("/api/v1/users/{$user->id}/restore");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User restored successfully'
                ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_admin_can_list_users_from_their_company(): void
    {
        // Crear una empresa
        $company = Company::factory()->create();
        
        // Crear un usuario admin para esa empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company.com',
            'company_id' => $company->id
        ]);
        $adminUser->assignRole('admin');
        
        // Crear algunos usuarios en la misma empresa
        User::factory()->count(3)->create(['company_id' => $company->id]);
        
        // Crear un usuario en otra empresa (no debería aparecer en la lista)
        User::factory()->create(['company_id' => Company::factory()->create()->id]);

        $response = $this->actingAs($adminUser)
                        ->getJson('/api/v1/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'company_id',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to'
                    ]
                ]);

        // Verificar que solo se muestran usuarios de la empresa del admin
        $responseData = $response->json('data');
        foreach ($responseData as $user) {
            $this->assertEquals($company->id, $user['company_id']);
        }
        
        // Verificar que el total es correcto (4 usuarios: admin + 3 creados)
        $this->assertEquals(4, $response->json('pagination.total'));
    }

    public function test_admin_cannot_access_users_from_other_companies(): void
    {
        // Crear dos empresas
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        
        // Crear un usuario admin para la primera empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company1.com',
            'company_id' => $company1->id
        ]);
        $adminUser->assignRole('admin');
        
        // Crear un usuario en la segunda empresa
        $userFromCompany2 = User::factory()->create([
            'email' => 'user@company2.com',
            'company_id' => $company2->id
        ]);

        $response = $this->actingAs($adminUser)
                        ->getJson('/api/v1/users');

        $response->assertStatus(200);
        
        // Verificar que no aparece el usuario de la otra empresa
        $responseData = $response->json('data');
        $userIds = collect($responseData)->pluck('id')->toArray();
        $this->assertNotContains($userFromCompany2->id, $userIds);
    }

    public function test_admin_can_list_users_with_sanctum_token(): void
    {
        // Crear una empresa
        $company = Company::factory()->create();
        
        // Crear un usuario admin para esa empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company.com',
            'company_id' => $company->id
        ]);
        $adminUser->assignRole('admin');
        
        // Crear algunos usuarios en la misma empresa
        User::factory()->count(3)->create(['company_id' => $company->id]);
        
        // Crear un token de Sanctum para el usuario
        $token = $adminUser->createToken('test-token');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
            'Accept' => 'application/json'
        ])->getJson('/api/v1/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'company_id',
                            'email_verified_at',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to'
                    ]
                ]);

        // Verificar que solo se muestran usuarios de la empresa del admin
        $responseData = $response->json('data');
        foreach ($responseData as $user) {
            $this->assertEquals($company->id, $user['company_id']);
        }
        
        // Verificar que el total es correcto (4 usuarios: admin + 3 creados)
        $this->assertEquals(4, $response->json('pagination.total'));
    }

    public function test_debug_admin_role_assignment(): void
    {
        // Crear una empresa
        $company = Company::factory()->create();
        
        // Crear un usuario admin para esa empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company.com',
            'company_id' => $company->id
        ]);
        $adminUser->assignRole('admin');
        
        // Verificar que el rol se asignó correctamente
        $this->assertTrue($adminUser->hasRole('admin'));
        $this->assertFalse($adminUser->hasRole('super-admin'));
        
        // Verificar que el usuario está autenticado
        $this->actingAs($adminUser);
        $this->assertTrue(auth()->check());
        $this->assertEquals($adminUser->id, auth()->id());
        
        // Verificar que el usuario tiene company_id
        $this->assertNotNull($adminUser->company_id);
        $this->assertEquals($company->id, $adminUser->company_id);
        
        // Debug: imprimir información del usuario
        dump([
            'user_id' => $adminUser->id,
            'email' => $adminUser->email,
            'company_id' => $adminUser->company_id,
            'roles' => $adminUser->getRoleNames()->toArray(),
            'has_role_admin' => $adminUser->hasRole('admin'),
            'has_role_super_admin' => $adminUser->hasRole('super-admin'),
            'auth_check' => auth()->check(),
            'auth_id' => auth()->id()
        ]);
    }

    public function test_debug_middleware_with_admin_user(): void
    {
        // Crear una empresa
        $company = Company::factory()->create();
        
        // Crear un usuario admin para esa empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company.com',
            'company_id' => $company->id
        ]);
        $adminUser->assignRole('admin');
        
        // Debug: verificar el middleware manualmente
        $middleware = new \App\Http\Middleware\CheckRole();
        
        // Crear una request mock
        $request = \Illuminate\Http\Request::create('/api/v1/users', 'GET');
        
        // Simular autenticación
        $this->actingAs($adminUser);
        
        // Verificar que el usuario está autenticado
        $this->assertTrue(Auth::check());
        $this->assertEquals($adminUser->id, Auth::id());
        
        // Debug: verificar roles
        dump([
            'user_roles' => $adminUser->getRoleNames()->toArray(),
            'has_admin_role' => $adminUser->hasRole('admin'),
            'has_super_admin_role' => $adminUser->hasRole('super-admin'),
            'auth_user_id' => Auth::id(),
            'auth_user_roles' => Auth::user()->getRoleNames()->toArray(),
        ]);
        
        // Simular el middleware manualmente
        $allowedRoles = ['super-admin', 'admin'];
        $hasRole = false;
        foreach ($allowedRoles as $role) {
            if (Auth::user()->hasRole(trim($role))) {
                $hasRole = true;
                break;
            }
        }
        
        dump([
            'allowed_roles' => $allowedRoles,
            'has_role_result' => $hasRole
        ]);
        
        $this->assertTrue($hasRole, 'El usuario admin debería tener uno de los roles permitidos');
    }

    public function test_debug_403_error_response(): void
    {
        // Crear una empresa
        $company = Company::factory()->create();
        
        // Crear un usuario admin para esa empresa
        $adminUser = User::factory()->create([
            'email' => 'admin@company.com',
            'company_id' => $company->id
        ]);
        $adminUser->assignRole('admin');
        
        // Crear un token de Sanctum para el usuario
        $token = $adminUser->createToken('test-token');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
            'Accept' => 'application/json'
        ])->getJson('/api/v1/users');

        // Debug: imprimir la respuesta completa
        dump([
            'status_code' => $response->status(),
            'response_body' => $response->json(),
            'user_id' => $adminUser->id,
            'user_roles' => $adminUser->getRoleNames()->toArray(),
            'has_admin_role' => $adminUser->hasRole('admin'),
            'token_plain_text' => $token->plainTextToken
        ]);
        
        // Verificar que el usuario tiene el rol correcto
        $this->assertTrue($adminUser->hasRole('admin'));
        
        // Si el test falla, mostrar más información
        if ($response->status() !== 200) {
            $this->fail('El usuario admin debería poder acceder a la lista de usuarios. Status: ' . $response->status() . ', Response: ' . json_encode($response->json()));
        }
    }
} 