<?php

namespace Tests\Feature\Plan;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

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
    }

    public function test_can_list_plans(): void
    {
        Plan::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin)
                        ->getJson('/api/v1/plans');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'monthly_price',
                            'annual_price',
                            'user_limit',
                            'user_limit_display',
                            'features',
                            'features_count',
                            'is_active',
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

    public function test_can_create_plan(): void
    {
        $planData = [
            'name' => 'Basic Plan',
            'monthly_price' => 29.99,
            'user_limit' => 10,
            'features' => [
                'User Management',
                'Basic Analytics',
                'Email Support'
            ]
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->postJson('/api/v1/plans', $planData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Plan created successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'monthly_price',
                        'annual_price',
                        'user_limit',
                        'user_limit_display',
                        'features',
                        'features_count',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ]);

        $this->assertDatabaseHas('plans', [
            'name' => 'Basic Plan',
            'monthly_price' => 29.99,
            'user_limit' => 10,
        ]);
    }

    public function test_cannot_create_plan_with_invalid_data(): void
    {
        $planData = [
            'name' => '', // Invalid: empty name
            'monthly_price' => -10, // Invalid: negative price
            'user_limit' => 0, // Invalid: zero users
            'features' => [] // Invalid: empty features
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->postJson('/api/v1/plans', $planData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'monthly_price', 'user_limit', 'features']);
    }

    public function test_can_show_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->superAdmin)
                        ->getJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'monthly_price' => $plan->monthly_price,
                        'user_limit' => $plan->user_limit
                    ]
                ]);
    }

    public function test_returns_404_for_nonexistent_plan(): void
    {
        $response = $this->actingAs($this->superAdmin)
                        ->getJson('/api/v1/plans/999');

        $response->assertStatus(404);
    }

    public function test_can_update_plan(): void
    {
        $plan = Plan::factory()->create();

        $updateData = [
            'name' => 'Updated Plan Name',
            'monthly_price' => 49.99,
            'user_limit' => 25,
            'features' => [
                'Advanced Analytics',
                'Priority Support',
                'Custom Integrations'
            ]
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->putJson("/api/v1/plans/{$plan->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Plan updated successfully'
                ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan Name',
            'monthly_price' => 49.99,
            'user_limit' => 25,
        ]);
    }

    public function test_can_update_plan_partially(): void
    {
        $plan = Plan::factory()->create();

        $updateData = [
            'name' => 'Partially Updated Plan'
        ];

        $response = $this->actingAs($this->superAdmin)
                        ->putJson("/api/v1/plans/{$plan->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Plan updated successfully'
                ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Partially Updated Plan',
        ]);
    }

    public function test_can_delete_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs($this->superAdmin)
                        ->deleteJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Plan deleted successfully'
                ]);

        $this->assertSoftDeleted('plans', ['id' => $plan->id]);
    }

    public function test_can_restore_plan(): void
    {
        $plan = Plan::factory()->create();
        $plan->delete();

        $response = $this->actingAs($this->superAdmin)
                        ->patchJson("/api/v1/plans/{$plan->id}/restore");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Plan restored successfully'
                ]);

        $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    }

    public function test_plan_response_includes_calculated_fields(): void
    {
        $plan = Plan::factory()->create([
            'monthly_price' => 29.99,
            'user_limit' => 10,
            'features' => json_encode(['feature1', 'feature2'])
        ]);

        $response = $this->actingAs($this->superAdmin)
                        ->getJson("/api/v1/plans/{$plan->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'annual_price' => 29.99 * 12,
                        'user_limit_display' => '10',
                        'features_count' => 2
                    ]
                ]);
    }
} 