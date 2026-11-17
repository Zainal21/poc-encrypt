<?php

namespace Database\Factories;

use App\Models\RegisteredKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RegisteredKeyFactory extends Factory
{
    protected $model = RegisteredKey::class;

    public function definition(): array
    {
        $keyId = Str::random(16);

        return [
            'key_id' => $keyId,
            'name' => fake()->word(),
            'algorithm' => 'RSA-OAEP-SHA256+AES-256-CBC',
            'public_key_path' => 'keys/public/'.$keyId.'.pem',
            'private_key_path' => 'keys/private/'.$keyId.'.pem',
            'fingerprint' => fake()->sha256(),
            'is_active' => true,
        ];
    }
}
