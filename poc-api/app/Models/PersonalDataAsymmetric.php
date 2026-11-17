<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalDataAsymmetric extends Model
{
    protected $table = 'personal_data_asymmetric';
    protected $fillable = ['registered_key_id','key_id','full_name','nik','email','phone','birth_date','address','raw_payload'];
    protected $casts = ['birth_date' => 'date', 'raw_payload' => 'array'];

    public function registeredKey(): BelongsTo
    {
        return $this->belongsTo(RegisteredKey::class, 'registered_key_id');
    }
}
