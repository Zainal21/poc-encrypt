<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDataSymmetric extends Model
{
    use HasFactory;

    protected $table = 'personal_data_symmetric';

    protected $fillable = [
        'full_name',
        'nik',
        'email',
        'phone',
        'birth_date',
        'address',
        'raw_payload',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'raw_payload' => 'array',
    ];
}
