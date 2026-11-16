<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalData extends Model
{
    protected $table = 'personal_data';

    protected $fillable = [
        'full_name',
        'nik',
        'phone_number',
    ];
}
