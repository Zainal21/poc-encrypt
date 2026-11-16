<?php

namespace Database\Factories;

use App\Models\PersonalDataAsymmetric;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalDataAsymmetricFactory extends Factory
{
    protected $model = PersonalDataAsymmetric::class;

    public function definition(): array
    {
        return [
            'key_id' => Str::random(16),
            'full_name' => fake()->name(),
            'nik' => fake()->numerify('###############').'1',
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'raw_payload' => [],
        ];
    }
}
