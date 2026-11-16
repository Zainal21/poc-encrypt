<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class SymmetricEncryptionService
{
    // AES-256-CBC via Laravel Crypt (uses APP_KEY)
    public function encrypt(string $plaintext): string
    {
        return Crypt::encryptString($plaintext);
    }

    public function decrypt(string $ciphertext): string
    {
        return Crypt::decryptString($ciphertext);
    }
}
