<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisteredKey extends Model
{
    use HasFactory;

    protected $table = 'registered_keys';

    protected $fillable = [
        'key_id',
        'name',
        'algorithm',
        'public_key_path',
        'private_key_path',
        'fingerprint',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function symmetricData(): HasMany
    {
        return $this->hasMany(PersonalDataSymmetric::class);
    }

    public function asymmetricData(): HasMany
    {
        return $this->hasMany(PersonalDataAsymmetric::class);
    }
}
