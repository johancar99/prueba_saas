<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 year', 'now');
        $endsAt = Carbon::parse($startsAt)->addMonth();

        return [
            'company_id' => \App\Models\Company::factory(),
            'plan_id' => \App\Models\Plan::factory(),
            'is_active' => fake()->boolean(90), // 90% probabilidad de estar activo
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    /**
     * Indicate that the subscription is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the subscription is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);
    }
} 