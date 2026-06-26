<?php

namespace Database\Factories;

use App\Domain\JobApplication\ValueObjects\ApplicationSource;
use App\Domain\JobApplication\ValueObjects\ApplicationStatus;
use App\Infrastructure\Persistence\Models\JobApplicationModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplicationModel>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplicationModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'position' => fake()->name(),
            'status' => fake()->randomElement(array_map(fn($case) => $case->value, ApplicationStatus::cases())),
            'source' => fake()->randomElement(array_map(fn($case) => $case->value, ApplicationSource::cases())),
            'applied_at' => now(),
            'location' => fake()->address(),
            'salary' => fake()->name(),
            'tags' => fake()->words(3),
            'is_remote' => fake()->boolean(),
            'offer_url' => fake()->url(),
            'notes' => fake()->text(),
        ];
    }

    public function invalid(): static
    {
        return $this->state(fn() => [
            'company' => fake()->company(),
            'position' => fake()->name(),
            'status' => 'invalid_status',
            'source' => fake()->randomElement(array_map(fn($case) => $case->value, ApplicationSource::cases())),
            'applied_at' => now(),
            'location' => fake()->address(),
            'salary' => fake()->name(),
            'tags' => fake()->words(3),
            'is_remote' => fake()->boolean(),
            'offer_url' => fake()->url(),
            'notes' => fake()->text(),
        ]);
    }
}
