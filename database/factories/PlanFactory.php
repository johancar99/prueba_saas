<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planNames = [
            'Basic Plan',
            'Professional Plan',
            'Enterprise Plan',
            'Starter Plan',
            'Premium Plan',
            'Business Plan',
            'Team Plan',
            'Individual Plan'
        ];

        $features = [
            'User Management',
            'Basic Analytics',
            'Email Support',
            'Advanced Analytics',
            'Priority Support',
            'Custom Integrations',
            'API Access',
            'White Label',
            'Multi-tenancy',
            'Advanced Security',
            'Custom Branding',
            'Dedicated Support'
        ];

        return [
            'name' => fake()->randomElement($planNames),
            'monthly_price' => fake()->randomFloat(2, 9.99, 999.99),
            'user_limit' => fake()->numberBetween(1, 1000),
            'features' => fake()->randomElements($features, fake()->numberBetween(3, 8)),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Indicate that the plan is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a basic plan.
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Plan',
            'monthly_price' => 29.99,
            'user_limit' => 10,
            'features' => [
                'User Management',
                'Basic Analytics',
                'Email Support'
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Create a professional plan.
     */
    public function professional(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Professional Plan',
            'monthly_price' => 99.99,
            'user_limit' => 50,
            'features' => [
                'User Management',
                'Advanced Analytics',
                'Priority Support',
                'API Access',
                'Custom Integrations'
            ],
            'is_active' => true,
        ]);
    }

    /**
     * Create an enterprise plan.
     */
    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Enterprise Plan',
            'monthly_price' => 299.99,
            'user_limit' => -1, // Unlimited
            'features' => [
                'User Management',
                'Advanced Analytics',
                'Priority Support',
                'API Access',
                'Custom Integrations',
                'White Label',
                'Multi-tenancy',
                'Advanced Security',
                'Custom Branding',
                'Dedicated Support'
            ],
            'is_active' => true,
        ]);
    }
}
