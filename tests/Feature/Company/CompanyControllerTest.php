<?php

namespace Tests\Feature\Company;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar el seeder de roles y permisos
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RoleAndPermissionSeeder']);
        
        // Crear un usuario super-admin para autenticación con email único
        $this->user = User::factory()->create([
            'email' => 'superadmin_' . uniqid() . '@example.com',
            'password' => bcrypt('password')
        ]);
        $this->user->assignRole('super-admin');

        // Crear un plan para las pruebas
        $this->plan = Plan::create([
            'name' => 'Plan Básico',
            'monthly_price' => 29.99,
            'user_limit' => 10,
            'features' => json_encode(['feature1', 'feature2'])
        ]);
    }

    public function test_can_list_companies(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
                        ->getJson('/api/v1/companies');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'phone',
                            'address',
                            'is_active',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination'
                ]);
    }

    public function test_can_create_company(): void
    {
        $companyData = [
            'name' => 'Nueva Empresa',
            'email' => 'nueva@empresa.com',
            'phone' => '987654321',
            'address' => 'Nueva Dirección',
            'is_active' => true,
            'plan_id' => $this->plan->id,
            'admin_email' => 'admin@empresa.com',
            'admin_password' => 'adminpassword',
            'admin_name' => 'Admin Empresa',
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'company' => [
                            'id',
                            'name',
                            'email',
                            'phone',
                            'address',
                            'is_active',
                            'created_at',
                            'updated_at'
                        ],
                        'admin_user' => [
                            'id',
                            'name',
                            'email',
                            'company_id',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'message'
                ])
                ->assertJson([
                    'message' => 'Empresa creada exitosamente'
                ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Nueva Empresa',
            'email' => 'nueva@empresa.com',
            'phone' => '987654321',
            'address' => 'Nueva Dirección',
            'is_active' => true
        ]);

        // Verificar que se creó la suscripción automáticamente
        $companyId = $response->json('data.company.id');
        $this->assertDatabaseHas('subscriptions', [
            'company_id' => $companyId,
            'plan_id' => $this->plan->id,
            'is_active' => true
        ]);

        // Verificar que se creó el usuario admin y tiene el rol admin
        $this->assertDatabaseHas('users', [
            'email' => 'admin@empresa.com',
            'company_id' => $companyId,
            'name' => 'Admin Empresa',
        ]);
        $adminUser = \App\Models\User::where('email', 'admin@empresa.com')->first();
        $this->assertTrue($adminUser->hasRole('admin'));
    }

    public function test_cannot_create_company_with_invalid_plan(): void
    {
        $companyData = [
            'name' => 'Nueva Empresa',
            'email' => 'nueva@empresa.com',
            'phone' => '987654321',
            'address' => 'Nueva Dirección',
            'is_active' => true,
            'plan_id' => 999,
            'admin_email' => 'admin@empresa.com',
            'admin_password' => 'adminpassword',
            'admin_name' => 'Admin Empresa',
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'El plan seleccionado no existe'
                ]);
    }

    public function test_cannot_create_company_with_duplicate_email(): void
    {
        Company::factory()->create(['email' => 'existing@empresa.com']);

        $companyData = [
            'name' => 'Nueva Empresa',
            'email' => 'existing@empresa.com',
            'phone' => '987654321',
            'address' => 'Nueva Dirección',
            'is_active' => true,
            'plan_id' => $this->plan->id,
            'admin_email' => 'admin@empresa.com',
            'admin_password' => 'adminpassword',
            'admin_name' => 'Admin Empresa',
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_can_show_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)
                        ->getJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'address',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $company->id,
                        'name' => $company->name,
                        'email' => $company->email
                    ]
                ]);
    }

    public function test_returns_404_for_nonexistent_company(): void
    {
        $response = $this->actingAs($this->user)
                        ->getJson('/api/v1/companies/999');

        $response->assertStatus(404);
    }

    public function test_can_update_company(): void
    {
        $company = Company::factory()->create();

        $updateData = [
            'name' => 'Empresa Actualizada',
            'email' => 'actualizada@empresa.com',
            'phone' => '123456789',
            'address' => 'Dirección Actualizada',
            'is_active' => false
        ];

        $response = $this->actingAs($this->user)
                        ->putJson("/api/v1/companies/{$company->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'address',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ],
                    'message'
                ])
                ->assertJson([
                    'message' => 'Empresa actualizada exitosamente'
                ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Empresa Actualizada',
            'email' => 'actualizada@empresa.com',
            'phone' => '123456789',
            'address' => 'Dirección Actualizada',
            'is_active' => false
        ]);
    }

    public function test_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)
                        ->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Empresa eliminada exitosamente'
                ]);

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_can_change_company_plan(): void
    {
        $company = Company::factory()->create();
        $newPlan = Plan::create([
            'name' => 'Plan Premium',
            'monthly_price' => 99.99,
            'user_limit' => 50,
            'features' => json_encode(['feature1', 'feature2', 'feature3'])
        ]);

        $changePlanData = [
            'plan_id' => $newPlan->id
        ];

        $response = $this->actingAs($this->user)
                        ->postJson("/api/v1/companies/{$company->id}/change-plan", $changePlanData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'company_id',
                        'plan_id',
                        'is_active',
                        'starts_at',
                        'ends_at',
                        'created_at',
                        'updated_at'
                    ],
                    'message'
                ])
                ->assertJson([
                    'message' => 'Plan cambiado exitosamente'
                ]);

        $this->assertDatabaseHas('subscriptions', [
            'company_id' => $company->id,
            'plan_id' => $newPlan->id,
            'is_active' => true
        ]);
    }

    public function test_cannot_change_company_plan_with_invalid_plan(): void
    {
        $company = Company::factory()->create();

        $changePlanData = [
            'plan_id' => 999
        ];

        $response = $this->actingAs($this->user)
                        ->postJson("/api/v1/companies/{$company->id}/change-plan", $changePlanData);

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'El plan seleccionado no existe'
                ]);
    }

    public function test_validation_errors_on_create(): void
    {
        $response = $this->actingAs($this->user)
                        ->postJson('/api/v1/companies', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'phone', 'address', 'plan_id', 'admin_email', 'admin_password']);
    }

    public function test_validation_errors_on_update(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)
                        ->putJson("/api/v1/companies/{$company->id}", [
                            'email' => 'invalid-email'
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
} 