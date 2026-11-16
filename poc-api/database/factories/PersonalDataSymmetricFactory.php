<?php

namespace Database\Factories;

use App\Models\PersonalDataSymmetric;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalDataSymmetricFactory extends Factory
{
    protected $model = PersonalDataSymmetric::class;

    public function definition(): array
    {
        return [
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
