<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reportable_type' => fake()->randomElement(['App\Models\Post', 'App\Models\User']),
            'reportable_id' => fake()->numberBetween(1,10),
            'status' => fake()->randomElement(['pending', 'accepted', 'denied']),
            'reason' => 'toxic'
        ];
    }
}
